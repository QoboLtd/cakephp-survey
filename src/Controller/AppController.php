<?php
/**
 * Copyright (c) Qobo Ltd. (https://www.qobo.biz)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Qobo Ltd. (https://www.qobo.biz)
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Qobo\Survey\Controller;

use App\Controller\AppController as BaseController;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Exception;

class AppController extends BaseController
{
    const DIRECTION_UP = 'up';
    const DIRECTION_DOWN = 'down';

    /**
     * @{inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * Change order of the record
     *
     * @param uuid $id of the instance
     * @param string $direction of the movement
     *
     * @return \Cake\Network\Response
     */
    public function move($id, $direction)
    {
        $table = TableRegistry::get('Qobo/Survey.' . $this->name);
        $direction = strtolower($direction);

        try {
            $entity = $table->get($id);

            if (self::DIRECTION_UP == $direction) {
                $table->moveUp($entity);
            }

            if (self::DIRECTION_DOWN == $direction) {
                $table->moveDown($entity);
            }
        } catch (Exception $e) {
            Log::warning($e->getMessage());
        }

        return $this->redirect($this->referer());
    }
}
