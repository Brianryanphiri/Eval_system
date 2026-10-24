<?php

namespace App\Domain;

final class CorrectiveAction
{
    public const ATTENDANCE_INTERVENTION = 'ATTENDANCE_INTERVENTION';
    public const MULTIMODAL_SUPPORT = 'MULTIMODAL_SUPPORT';
    public const CLINICAL_REFERRAL = 'CLINICAL_REFERRAL';
    public const RECLASSIFY = 'RECLASSIFY';
    public const NO_ACTION = 'NO_ACTION';
    public const GATHER_MORE_DATA = 'GATHER_MORE_DATA';
}