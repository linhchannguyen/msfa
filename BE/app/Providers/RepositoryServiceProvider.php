<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Auth\AuthRepository;
use App\Repositories\File\FileRepository;
use App\Repositories\Home\HomeRepository;
use App\Repositories\Item\ItemRepository;
use App\Repositories\Stock\StockRepository;
use App\Repositories\Auth\AuthCacheDecorator;
use App\Repositories\Home\HomeCacheDecorator;
use App\Repositories\Inform\InformRepository;
use App\Repositories\Region\RegionRepository;
use App\Repositories\Develop\DevelopRepository;
use App\Repositories\Message\MessageRepository;
use App\Repositories\Calendar\CalendarRepository;
use App\Repositories\Material\MaterialRepository;
use App\Repositories\RoleUser\RoleUserRepository;
use App\Repositories\Schedule\ScheduleRepository;
use App\Repositories\Auth\AuthRepositoryInterface;
use App\Repositories\File\FileRepositoryInterface;
use App\Repositories\Home\HomeRepositoryInterface;
use App\Repositories\Item\ItemRepositoryInterface;
use App\Repositories\Stock\StockRepositoryInterface;
use App\Repositories\RolePolicy\RolePolicyRepository;
use App\Repositories\Unapproved\UnapprovedRepository;
use App\Repositories\Inform\InformRepositoryInterface;
use App\Repositories\Region\RegionRepositoryInterface;
use App\Repositories\DailyReport\DailyReportRepository;
use App\Repositories\PersonGroup\PersonGroupRepository;
use App\Repositories\Develop\DevelopRepositoryInterface;
use App\Repositories\Message\MessageRepositoryInterface;
use App\Repositories\FacilityUser\FacilityUserRepository;
use App\Repositories\Organization\OrganizationRepository;
use App\Repositories\PersonSearch\PersonSearchRepository;
use App\Repositories\Calendar\CalendarRepositoryInterface;
use App\Repositories\Material\MaterialRepositoryInterface;
use App\Repositories\RoleUser\RoleUserRepositoryInterface;
use App\Repositories\Schedule\ScheduleRepositoryInterface;
use App\Repositories\ConditionArea\ConditionAreaRepository;
use App\Repositories\FacilityGroup\FacilityGroupRepository;
use App\Repositories\AccountSetting\AccountSettingRepository;
use App\Repositories\FacilitySearch\FacilitySearchRepository;
use App\Repositories\InformCategory\InformCategoryRepository;
use App\Repositories\UserManagement\UserManagementRepository;
use App\Repositories\WatchUserColor\WatchUserColorRepository;
use App\Repositories\PasswordReissue\PasswordReissueRepositoryInterface;
use App\Repositories\RolePolicy\RolePolicyRepositoryInterface;
use App\Repositories\Unapproved\UnapprovedRepositoryInterface;
use App\Repositories\PasswordReissue\PasswordReissueRepository;
use App\Repositories\SystemParameter\SystemParameterRepository;
use App\Repositories\DailyReport\DailyReportRepositoryInterface;
use App\Repositories\PersonGroup\PersonGroupRepositoryInterface;
use App\Repositories\FacilityPersonal\FacilityPersonalRepository;
use App\Repositories\InterviewContent\InterviewContentRepository;
use App\Repositories\InterviewDetails\InterviewDetailsRepository;
use App\Repositories\PersonDetailNote\PersonDetailNoteRepository;
use App\Repositories\FacilityUser\FacilityUserRepositoryInterface;
use App\Repositories\Organization\OrganizationRepositoryInterface;
use App\Repositories\PersonSearch\PersonSearchRepositoryInterface;
use App\Repositories\PermissionSetting\PermissionSettingRepository;
use App\Repositories\PersonGroupDetail\PersonGroupDetailRepository;
use App\Repositories\ConditionArea\ConditionAreaRepositoryInterface;
use App\Repositories\FacilityGroup\FacilityGroupRepositoryInterface;
use App\Repositories\AccountSetting\AccountSettingRepositoryInterface;
use App\Repositories\FacilitySearch\FacilitySearchRepositoryInterface;
use App\Repositories\InformCategory\InformCategoryRepositoryInterface;
use App\Repositories\UserManagement\UserManagementRepositoryInterface;
use App\Repositories\WatchUserColor\WatchUserColorRepositoryInterface;
use App\Repositories\AttendantManagement\AttendantManagementRepository;
use App\Repositories\FacilityGroupDetail\FacilityGroupDetailRepository;
use App\Repositories\PersonDetailNetwork\PersonDetailNetworkRepository;
use App\Repositories\SystemParameter\SystemParameterRepositoryInterface;
use App\Repositories\FacilityDetailsNotes\FacilityDetailsNotesRepository;
use App\Repositories\PersonDetailTimeline\PersonDetailTimelineRepository;
use App\Repositories\FacilityPersonal\FacilityPersonalRepositoryInterface;
use App\Repositories\InterviewContent\InterviewContentRepositoryInterface;
use App\Repositories\InterviewDetails\InterviewDetailsRepositoryInterface;
use App\Repositories\PersonDetailNote\PersonDetailNoteRepositoryInterface;
use App\Repositories\FacilityDetailsTime\FacilityDetailsTimeLineRepository;
use App\Repositories\UtilizationPointTotal\UtilizationPointTotalRepository;
use App\Repositories\PermissionSetting\PermissionSettingRepositoryInterface;
use App\Repositories\PersonGroupDetail\PersonGroupDetailRepositoryInterface;
use App\Repositories\InterviewDetailedInput\InterviewDetailedInputRepository;
use App\Repositories\OrganizationManagement\OrganizationManagementRepository;
use App\Repositories\AttendantManagement\AttendantManagementRepositoryInterface;
use App\Repositories\FacilityGroupDetail\FacilityGroupDetailRepositoryInterface;
use App\Repositories\PersonDetailNetwork\PersonDetailNetworkRepositoryInterface;
use App\Repositories\ConventionSearch\ConventionSearchRepository;
use App\Repositories\ConventionSearch\ConventionSearchRepositoryInterface;
use App\Repositories\FacilityDetailsNotes\FacilityDetailsNotesRepositoryInterface;
use App\Repositories\PersonDetailTimeline\PersonDetailTimelineRepositoryInterface;
use App\Repositories\FacilityDetailsTime\FacilityDetailsTimeLineRepositoryInterface;
use App\Repositories\UtilizationPointTotal\UtilizationPointTotalRepositoryInterface;
use App\Repositories\InterviewDetailedInput\InterviewDetailedInputRepositoryInterface;
use App\Repositories\OrganizationManagement\OrganizationManagementRepositoryInterface;
use App\Repositories\AttendantCollectiveRegistration\AttendantCollectiveRegistrationRepositoryInterface;
use App\Repositories\CustomMaterialManagement\CustomMaterialManagementRepository;
use App\Repositories\CustomMaterialManagement\CustomMaterialManagementRepositoryInterface;
use App\Repositories\DocumentSearch\DocumentSearchRepository;
use App\Repositories\DocumentSearch\DocumentSearchRepositoryInterface;
use App\Repositories\LectureSeriesSelection\LectureSeriesSelectionRepositoryInterface;
use App\Repositories\LectureSeriesSelection\LectureSeriesSelectionRepository;
use App\Repositories\AttendantCollectiveRegistration\AttendantCollectiveRegistrationRepository;
use App\Repositories\BriefingSearch\BriefingSearchRepository;
use App\Repositories\BriefingSearch\BriefingSearchRepositoryInterface;
use App\Repositories\ContactWholesalerMasterReflection\ContactWholesalerMasterReflectionRepository;
use App\Repositories\ContactWholesalerMasterReflection\ContactWholesalerMasterReflectionRepositoryInterface;
use App\Repositories\TargetFacilityManagement\TargetFacilityManagementRepositoryInterface;
use App\Repositories\MaterialEvaluationInput\MaterialEvaluationInputRepositoryInterface;
use App\Repositories\MaterialEvaluationInput\MaterialEvaluationInputRepository;
use App\Repositories\RecommendedPeriodConfirmation\RecommendedPeriodConfirmationRepository;
use App\Repositories\PersonalDetailsWorkingInformation\PersonalDetailsWorkingInformationRepositoryInterface;
use App\Repositories\PersonalDetailsWorkingInformation\PersonalDetailsWorkingInformationRepository;
use App\Repositories\RecommendedPeriodConfirmation\RecommendedPeriodConfirmationRepositoryInterface;
use App\Repositories\TimelineGeneration\TimelineGenerationRepository;
use App\Repositories\TimelineGeneration\TimelineGenerationRepositoryInterface;
use App\Repositories\DocumentPowerPoint\DocumentPowerPointRepositoryInterface;
use App\Repositories\DocumentPowerPoint\DocumentPowerPointRepository;
use App\Repositories\InFacilityTargetPersonManagement\InFacilityTargetPersonManagementRepositoryInterface;
use App\Repositories\InFacilityTargetPersonManagement\InFacilityTargetPersonManagementRepository;
use App\Repositories\QAManagement\QAManagementRepository;
use App\Repositories\QAManagement\QAManagementRepositoryInterface;
use App\Repositories\TargetFacilityManagement\TargetFacilityManagementRepository;
use App\Repositories\TargetFacilityPersonSetting\TargetFacilityPersonSettingRepositoryInterface;
use App\Repositories\TargetFacilityPersonSetting\TargetFacilityPersonSettingRepository;
use App\Repositories\TargetSelectionPeriodManagement\TargetSelectionPeriodManagementRepositoryInterface;
use App\Repositories\TargetSelectionPeriodManagement\TargetSelectionPeriodManagementRepository;
use App\Repositories\DocumentUsageSituation\DocumentUsageSituationRepositoryInterface;
use App\Repositories\DocumentUsageSituation\DocumentUsageSituationRepository;
use App\Repositories\KnowledgeInput\KnowledgeInputRepository;
use App\Repositories\KnowledgeInput\KnowledgeInputRepositoryInterface;
use App\Repositories\VariableDefinition\VariableDefinitionRepository;
use App\Repositories\VariableDefinition\VariableDefinitionRepositoryInterface;
use App\Repositories\KnowledgeManagement\KnowledgeManagementRepository;
use App\Repositories\KnowledgeManagement\KnowledgeManagementRepositoryInterface;
use App\Repositories\TimelineSearch\TimelineSearchRepository;
use App\Repositories\TimelineSearch\TimelineSearchRepositoryInterface;
use App\Repositories\ManagerQuestionAndAwnser\ManagerQuestionAndAwnserInterface;
use App\Repositories\ManagerQuestionAndAwnser\ManagerQuestionAndAwnserRepository;
use App\Repositories\MovePersonMaintenance\MovePersonMaintenanceRepository;
use App\Repositories\MovePersonMaintenance\MovePersonMaintenanceRepositoryInterface;
use App\Repositories\PostedUserManagement\PostedUserManagementRepository;
use App\Repositories\PostedUserManagement\PostedUserManagementRepositoryInterface;
use App\Repositories\ManagementFile\ManagementFileInterface;
use App\Repositories\ManagementFile\ManagementFileRepository;
use App\Repositories\GetSystemParameter\GetSystemParameterRepository;
use App\Repositories\GetSystemParameter\GetSystemParameterRepositoryInterface;
use App\Repositories\TargetFacilityPersonSettingJob\TargetFacilityPersonSettingJobRepository;
use App\Repositories\TargetFacilityPersonSettingJob\TargetFacilityPersonSettingJobRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(HomeRepositoryInterface::class, HomeRepository::class);
        $this->app->bind(OrganizationRepositoryInterface::class, OrganizationRepository::class);
        $this->app->bind(MessageRepositoryInterface::class, MessageRepository::class);
        $this->app->bind(OrganizationManagementRepositoryInterface::class, OrganizationManagementRepository::class);
        $this->app->bind(ItemRepositoryInterface::class, ItemRepository::class);
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(InformRepositoryInterface::class, InformRepository::class);
        $this->app->bind(DevelopRepositoryInterface::class, DevelopRepository::class);
        $this->app->bind(RegionRepositoryInterface::class, RegionRepository::class);
        $this->app->bind(AccountSettingRepositoryInterface::class, AccountSettingRepository::class);
        $this->app->bind(MaterialRepositoryInterface::class, MaterialRepository::class);
        $this->app->bind(InformCategoryRepositoryInterface::class, InformCategoryRepository::class);
        $this->app->bind(ConditionAreaRepositoryInterface::class, ConditionAreaRepository::class);
        $this->app->bind(FacilityPersonalRepositoryInterface::class, FacilityPersonalRepository::class);
        $this->app->bind(SystemParameterRepositoryInterface::class, SystemParameterRepository::class);
        $this->app->bind(UserManagementRepositoryInterface::class, UserManagementRepository::class);
        $this->app->bind(PermissionSettingRepositoryInterface::class, PermissionSettingRepository::class);
        $this->app->bind(PasswordReissueRepositoryInterface::class, PasswordReissueRepository::class);
        $this->app->bind(StockRepositoryInterface::class, StockRepository::class);
        $this->app->bind(FileRepositoryInterface::class, FileRepository::class);
        $this->app->bind(DailyReportRepositoryInterface::class, DailyReportRepository::class);
        $this->app->bind(CalendarRepositoryInterface::class, CalendarRepository::class);
        $this->app->bind(RecommendedPeriodConfirmationRepositoryInterface::class, RecommendedPeriodConfirmationRepository::class);
        $this->app->bind(UnapprovedRepositoryInterface::class, UnapprovedRepository::class);
        $this->app->bind(FacilityGroupRepositoryInterface::class, FacilityGroupRepository::class);
        $this->app->bind(InterviewContentRepositoryInterface::class, InterviewContentRepository::class);
        $this->app->bind(PersonGroupRepositoryInterface::class, PersonGroupRepository::class);
        $this->app->bind(ScheduleRepositoryInterface::class, ScheduleRepository::class);
        $this->app->bind(WatchUserColorRepositoryInterface::class,WatchUserColorRepository::class);
        $this->app->bind(RolePolicyRepositoryInterface::class, RolePolicyRepository::class);
        $this->app->bind(PersonDetailNoteRepositoryInterface::class, PersonDetailNoteRepository::class);
        $this->app->bind(InterviewDetailsRepositoryInterface::class, InterviewDetailsRepository::class);
        $this->app->bind(FacilitySearchRepositoryInterface::class, FacilitySearchRepository::class);
        $this->app->bind(PersonDetailNetworkRepositoryInterface::class, PersonDetailNetworkRepository::class);
        $this->app->bind(PersonDetailTimelineRepositoryInterface::class, PersonDetailTimelineRepository::class);
        $this->app->bind(PersonSearchRepositoryInterface::class, PersonSearchRepository::class);
        $this->app->bind(PersonalDetailsWorkingInformationRepositoryInterface::class, PersonalDetailsWorkingInformationRepository::class);
        $this->app->bind(FacilityDetailsTimeLineRepositoryInterface::class, FacilityDetailsTimeLineRepository::class);
        $this->app->bind(InterviewDetailedInputRepositoryInterface::class, InterviewDetailedInputRepository::class);
        $this->app->bind(FacilityDetailsNotesRepositoryInterface::class, FacilityDetailsNotesRepository::class);
        $this->app->bind(UtilizationPointTotalRepositoryInterface::class, UtilizationPointTotalRepository::class);
        $this->app->bind(FacilityGroupDetailRepositoryInterface::class, FacilityGroupDetailRepository::class);
        $this->app->bind(PersonGroupDetailRepositoryInterface::class, PersonGroupDetailRepository::class);
        $this->app->bind(RoleUserRepositoryInterface::class, RoleUserRepository::class);
        $this->app->bind(FacilityUserRepositoryInterface::class, FacilityUserRepository::class);
        $this->app->bind(AttendantManagementRepositoryInterface::class, AttendantManagementRepository::class);
        $this->app->bind(TimelineGenerationRepositoryInterface::class, TimelineGenerationRepository::class);
        $this->app->bind(ConventionSearchRepositoryInterface::class, ConventionSearchRepository::class);
        $this->app->bind(LectureSeriesSelectionRepositoryInterface::class, LectureSeriesSelectionRepository::class);
        $this->app->bind(MaterialEvaluationInputRepositoryInterface::class, MaterialEvaluationInputRepository::class);
        $this->app->bind(DocumentSearchRepositoryInterface::class, DocumentSearchRepository::class);
        $this->app->bind(AttendantCollectiveRegistrationRepositoryInterface::class, AttendantCollectiveRegistrationRepository::class);
        $this->app->bind(CustomMaterialManagementRepositoryInterface::class, CustomMaterialManagementRepository::class);
        $this->app->bind(DocumentPowerPointRepositoryInterface::class, DocumentPowerPointRepository::class);
        $this->app->bind(TargetFacilityManagementRepositoryInterface::class, TargetFacilityManagementRepository::class);
        $this->app->bind(QAManagementRepositoryInterface::class, QAManagementRepository::class);
        $this->app->bind(InFacilityTargetPersonManagementRepositoryInterface::class, InFacilityTargetPersonManagementRepository::class);
        $this->app->bind(TargetFacilityPersonSettingRepositoryInterface::class, TargetFacilityPersonSettingRepository::class);
        $this->app->bind(TargetSelectionPeriodManagementRepositoryInterface::class, TargetSelectionPeriodManagementRepository::class);
        $this->app->bind(DocumentUsageSituationRepositoryInterface::class, DocumentUsageSituationRepository::class);
        $this->app->bind(BriefingSearchRepositoryInterface::class, BriefingSearchRepository::class);
        $this->app->bind(VariableDefinitionRepositoryInterface::class, VariableDefinitionRepository::class);
        $this->app->bind(KnowledgeManagementRepositoryInterface::class, KnowledgeManagementRepository::class);
        $this->app->bind(TimelineSearchRepositoryInterface::class, TimelineSearchRepository::class);
        $this->app->bind(ManagerQuestionAndAwnserInterface::class, ManagerQuestionAndAwnserRepository::class);
        $this->app->bind(MovePersonMaintenanceRepositoryInterface::class, MovePersonMaintenanceRepository::class);
        $this->app->bind(ContactWholesalerMasterReflectionRepositoryInterface::class, ContactWholesalerMasterReflectionRepository::class);
        $this->app->bind(KnowledgeInputRepositoryInterface::class, KnowledgeInputRepository::class);
        $this->app->bind(PostedUserManagementRepositoryInterface::class, PostedUserManagementRepository::class);
        $this->app->bind(ManagementFileInterface::class, ManagementFileRepository::class);
        $this->app->bind(GetSystemParameterRepositoryInterface::class, GetSystemParameterRepository::class);
        $this->app->bind(TargetFacilityPersonSettingJobRepositoryInterface::class, TargetFacilityPersonSettingJobRepository::class);
        // case use cache
        // $this->app->bind(HomeRepositoryInterface::class, HomeCacheDecorator::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
