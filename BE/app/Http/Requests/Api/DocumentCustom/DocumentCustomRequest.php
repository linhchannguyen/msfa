<?php

namespace App\Http\Requests\Api\DocumentCustom;

use App\Http\Requests\Api\ApiBaseRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Repositories\DocumentPowerPoint\DocumentPowerPointRepositoryInterface;
use App\Traits\AuthTrait;

class DocumentCustomRequest extends ApiBaseRequest {
    use AuthTrait;

    private $documentCustomRepository;
    public function __construct(DocumentPowerPointRepositoryInterface $documentCustomRepository)
    {
        $this->documentCustomRepository = $documentCustomRepository;
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
    */
    public function rules() {
        $action = Route::currentRouteName();
        $userCdLogin = $this->getCurrentUser();
        if ( $action == 'D.api.document-custom.create' && $this->method() == 'GET' ) {
            return [];
        }
        if ( $action == 'D.api.document-custom.update' || $action == 'D.api.document-custom.delete' ) {
            // check id isset
            $param = [
                [
                    "table" => "t_document_composition",
                    "condition" => [
                        "document_id" => $this->id
                    ]
                ]
            ];
            $this->validateInputParamater($param);

            $rules['id'] = ['nullable', 'integer','exists:t_document_composition,document_id', function ($attribute, $value, $fail) use ($userCdLogin) {
                $documentCustomInput = DB::select('SELECT COUNT(*) AS total_exist_document_custom FROM t_document WHERE document_type = 20 AND document_id = :document_id', ['document_id' => $value]);
                if($documentCustomInput[0]->total_exist_document_custom == 0) {
                    return $fail(__('validation.exists', ['attribute' => 'ID']));
                }
                // check user has access into document
                $listDocumentOriginal = $this->documentCustomRepository->getListOriginalDocumentFromDocumentCustom($value);
                if ( !$this->documentCustomRepository->isUserCanBeCreateListDocument($listDocumentOriginal, $userCdLogin) ) {
                    return $fail(__('document_custom.list_origanation_of_document_not_isset_origanation_of_user'));
                }
            }];
            if ( $this->method() == 'GET' || $action == 'D.api.document-custom.delete' ) {
                return $rules;
            }
        }
        if ( $action == 'D.api.document.slide-show' ) {
            // check id isset
            $param = [
                [
                    "table" => "t_document",
                    "condition" => [
                        "document_id" => $this->id
                    ]
                ]
            ];
            $this->validateInputParamater($param);

            $rules['id'] = ['nullable', 'exists:t_document,document_id', function ($attribute, $value, $fail) use ($userCdLogin) {
                $listDocumentOriginal = $this->documentCustomRepository->getListOriginalDocumentFromDocumentCustom($value);
                // check user has access into document
                if ( !$this->documentCustomRepository->isUserCanBeCreateListDocument($listDocumentOriginal, $userCdLogin) ) {
                    return $fail(__('document_custom.list_origanation_of_document_not_isset_origanation_of_user'));
                }
            }];

            return $rules;
        }
        
        $rules['document_name'] = ['required', 'string', 'max:50'];
        $rules['product_cd'] = ['nullable', 'exists:m_product,product_cd'];
        $rules['disease'] = ['nullable', 'string', 'max:50'];
        $rules['medical_subjects_group_cd'] = ['nullable', 'exists:m_medical_subjects_group,medical_subjects_group_cd'];
        $rules['safety_information_flag'] = ['required', 'numeric', 'in:0,1'];
        $rules['off_label_information_flag'] = ['required', 'numeric', 'in:0,1'];
        $rules['document_contents'] = ['nullable', 'string', 'max:300'];
        $rules['document_copy'] = ['nullable', 'required_if:type_create_copy,1', 'exists:t_document_composition,document_id'];
        $rules['document_custom_slides'] = ['required', function ($attribute, $value, $fail) use ($userCdLogin) {
            /**
             * {
             *      content : string,
             *      page_num : integer,
             *      original_document_id : integer,
             *      original_document_page_num : integer
             * }
             */
            if ( $value ) {
                $documentCustoms = json_decode($value, true);
                if ( count($documentCustoms) == 0 ) {
                    return $fail(__('document_custom.need_choose_atleast_one_slide'));
                }
                $pageNums = [];
                $listDocumentOriginalInput = [];
                foreach($documentCustoms as $documentCustom) {
                    $content = $documentCustom['content'];
                    $pageNum = $documentCustom['page_num'];
                    $originalDocumentId = trim($documentCustom['original_document_id']);
                    $originalDocumentPageNum = $documentCustom['original_document_page_num'];
                    $listDocumentOriginalInput[] = $originalDocumentId;
                    if (!$pageNum || !$originalDocumentId || !$originalDocumentPageNum) {
                        return $fail(__('validation.exists', ['attribute' => '属性']));
                        break;
                    }
                    $dataOriginal = DB::select("SELECT T1.document_id, M1.cover_flag  
                    FROM t_original_document_detail T1 
                    JOIN m_document_category M1 ON M1.document_category_cd = T1.document_category_cd 
                    WHERE document_id = $originalDocumentId");
                    if ( count($dataOriginal) == 0 ) {
                        return $fail(__('validation.exists', ['attribute' => '資材ID ']));
                        break;
                    }
                    if ( count($pageNums) && in_array($pageNum, $pageNums) ) {
                        return $fail(__('validation.in', ['attribute' => 'ページ番号 ']));
                        break;
                    } else {
                        $pageNums[] = $pageNum;
                    }
                }
                // check document is same org
                if ( count($listDocumentOriginalInput) && !$this->documentCustomRepository->isDocumentsSameOrg($listDocumentOriginalInput) ) {
                    return $fail(__('document_custom.documents_not_same_group_origanation'));
                }
                // check user has access into document
                if ( !$this->documentCustomRepository->isUserCanBeCreateListDocument($listDocumentOriginalInput, $userCdLogin) ) {
                    return $fail(__('document_custom.list_origanation_of_document_not_isset_origanation_of_user'));
                }
            }
        }];

        return $rules;
    }

    public function messages() {
        return [
            'id.exists' => __('validation.exists', ['attribute' => 'ID']),
            'document_custom_slides.required' => __('document_custom.need_choose_atleast_one_slide'),
        ];
    }
}
