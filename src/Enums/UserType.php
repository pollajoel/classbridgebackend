<?php
namespace App\Enums;

enum UserType: string {
    case PARENT  = 'ROLE_PARENT';
    case STUDENT = 'ROLE_STUDENT';
    case TEACHER = 'ROLE_TEACHER';
    case USER    = 'ROLE_USER';
    case ADMIN   =  'ROLE_ADMIN';
}

