<?php

namespace App\Domain;

final class ReasonCode
{
    public const LOW_ATTENDANCE = 'LOW_ATTENDANCE';
    public const AMBIGUOUS_PROFILE = 'AMBIGUOUS_PROFILE';
    public const CLINICAL_REFERRAL = 'CLINICAL_REFERRAL';
    public const MISPLACED = 'MISPLACED';
    public const CORRECTLY_PLACED = 'CORRECTLY_PLACED';
    public const INSUFFICIENT_DATA = 'INSUFFICIENT_DATA';
}

final class CorrectiveAction
{
    public const ATTENDANCE_INTERVENTION = 'ATTENDANCE_INTERVENTION';
    public const MULTIMODAL_SUPPORT = 'MULTIMODAL_SUPPORT';
    public const CLINICAL_REFERRAL = 'CLINICAL_REFERRAL';
    public const RECLASSIFY = 'RECLASSIFY';
    public const NO_ACTION = 'NO_ACTION';
    public const GATHER_MORE_DATA = 'GATHER_MORE_DATA';
}