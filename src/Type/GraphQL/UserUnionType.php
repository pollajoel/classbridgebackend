<?php

// src/GraphQL/Type/UserUnionType.php
namespace App\GraphQL\Type;

use App\Entity\User;
use App\Entity\ParentEntity;
use App\Entity\StudentEntity;
use App\Entity\TeacherEntity;
use GraphQL\Type\Definition\UnionType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;

class UserUnionType extends UnionType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'UserUnion',  // Le nom de l'union dans GraphQL
            'types' => [
                new ObjectType([
                    'name' => 'User',
                    'fields' => [
                        'username' => Type::string(),
                        'email' => Type::string(),
                        // Ajoutez ici d'autres champs que vous souhaitez exposer
                    ]
                ]),
                new ObjectType([
                    'name' => 'ParentEntity',
                    'fields' => [
                        'id' => Type::int(),
                        'name' => Type::string(),
                        // Ajoutez ici d'autres champs pour ParentEntity
                    ]
                ]),
                new ObjectType([
                    'name' => 'StudentEntity',
                    'fields' => [
                        'id' => Type::int()
                        // Ajoutez ici d'autres champs pour StudentEntity
                    ]
                ]),
                new ObjectType([
                    'name' => 'TeacherEntity',
                    'fields' => [
                        'id' => Type::int(),
                        'subject' => Type::string(),
                        // Ajoutez ici d'autres champs pour TeacherEntity
                    ]
                ])
            ],
            'resolveType' => function ($value) {
                if ($value instanceof User) {
                    return 'User'; // Correspond à la définition du type 'User' dans le schéma GraphQL
                } elseif ($value instanceof ParentEntity) {
                    return 'ParentEntity';
                } elseif ($value instanceof StudentEntity) {
                    return 'StudentEntity';
                } elseif ($value instanceof TeacherEntity) {
                    return 'TeacherEntity';
                }
                return null;
            },
        ]);
    }
}
