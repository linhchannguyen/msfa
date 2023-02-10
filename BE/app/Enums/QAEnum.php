<?php

namespace App\Enums;

class QAEnum extends Enum
{
    const QUESTION = 'question';
    const ANSWER = 'answer';
    const TYPE_ACTION_BEST_ANSWER = 'best_answer';
    const TYPE_ACTION_CREATE_ANSWER = 'create_answer';
    const TYPE_ACTION_UNSUITABLE_REPORT = 'unsuitable_report';
    const ANSWER_NORMAL = 0;
    const ANSWER_BEST = 1;
}