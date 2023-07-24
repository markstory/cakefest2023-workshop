<?php
declare(strict_types=1);

namespace App\Policy;

use Authorization\Exception\ForbiddenException;

/**
 * Indicate that the current action requires sudo to be
 * activated. This exception is raised by policies and handled
 * by application error handling to render a sudo form.
 */
class SudoRequiredException extends ForbiddenException
{
}
