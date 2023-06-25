<?php
declare(strict_types=1);

namespace App\Model;

enum ArticleStatus: string {
    case DRAFT = 'DRAFT';
    case IN_REVIEW = 'IN_REVIEW';
    case PUBLISHED = 'PUBLISHED';
}
