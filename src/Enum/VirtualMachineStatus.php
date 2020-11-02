<?php declare(strict_types = 1);

namespace App\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static VirtualMachineStatus ONLINE()
 * @method static VirtualMachineStatus OFFLINE()
 * @method static VirtualMachineStatus CREATED()
 */
class VirtualMachineStatus extends Enum
{

    private const ONLINE = 'ONLINE';
    private const OFFLINE = 'OFFLINE';
    private const CREATED = 'CREATED';

}
