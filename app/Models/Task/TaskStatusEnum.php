<?php

declare(strict_types=1);

namespace App\Models\Task;

enum TaskStatusEnum: string
{
    case ACTIVE = 'В работе';
    case COMPLETED = 'Завершена';
    case NEW = 'Новая';
}