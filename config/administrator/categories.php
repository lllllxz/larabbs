<?php

return [
    'title' => '分类',
    'single' => '分类',
    'model' => \App\Models\Category::class,

    //对CURD的单独权限控制，其他动作不指定默认为通过
    'action_permissions' => [
        'delete' => function () {
            return Auth::user()->hasRole('Founder');
        }
    ],

    'columns' => [
        'id' => [
            'title' => 'ID'
        ],
        'name' => [
            'title' => '名称',
            'sortable' => false,
        ],
        'description' =>[
            'title' => '描述',
            'sortable' => false
        ],
        'operation' => [
            'title' => '管理',
            'sortable' => false
        ],
    ],

    'edit_fields' => [
        'name' => [
            'title' => '名称'
        ],
        'description' => [
            'title' => '描述',
            'type' => 'textarea'
        ],
    ],

    'filters' => [
        'id' => [
            'title' => '分类 ID',
        ],
        'name' => [
            'title' => '名称'
        ],
        'description' => [
            'title' => '描述'
        ]
    ],

    'rules' => [
        'name' => 'required|min:1|unique:categries'
    ],

    'messages' => [
        'name.unique' => '该分类名已存在，请更换',
        'name.required' => '分类名请确保一个字符以上'
    ]
];
