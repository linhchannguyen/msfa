<?php

namespace App\Enums;

class InformUrl extends Enum
{
    const DAILY_REPORT = 'report_id';
    const PERSON_DETAIL_NOTES = PERSON_CD;
    const SCHEDULE_DETAILS_SETTING = 'schedule_id';
    const KNOWLEDGE_DETAIL = KNOWLEDGE_ID;
    const FACILITY_DETAIL = 'facility_cd';
    const CONVENTION_INPUT = 'convention_id';
    const CALENDAR = 'calendar';
    const KNOWLEDGE_INPUT = 'knowledge_id';
    const BRIEFING_INPUT = 'briefing_id';
    const QA_SEARCH = 'question_id';

    // Map status with language file in /resources/lang/file.php
    const __DESCRIPTIONS__ = [
        'BRIEFING_INPUT' => '/briefing-session-input?briefing_id=',
        'DAILY_REPORT' => '/report-input?reportId=',
        'PERSON_DETAIL_NOTES' => '/person-details/',
        'SCHEDULE_DETAILS_SETTING' => '/schedule-details-setting',
        'KNOWLEDGE_DETAIL' => '/knowledge-details',
        'FACILITY_DETAIL' => '/facility-details/',
        'CONVENTION_INPUT' => '/convention-input',
        'CALENDAR' => '/calendar',
        'KNOWLEDGE_INPUT' => '/knowledge-input',
        'QA_SEARCH' => '/qa-search',
    ];
}