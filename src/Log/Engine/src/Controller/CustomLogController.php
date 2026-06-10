<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Log\Engine\CustomFileLog;
use Cake\Event\EventInterface;
use Cake\Http\Response;

class CustomLogController extends AppController
{
    /**
     * BeforeFilter lifecycle hook.
     * 
     * @param \Cake\Event\EventInterface $event An Event instance
     * @return \Cake\Http\Response|null|void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        
        // Modern CakePHP Authorization check syntax
        if ($this->components()->has('Auth')) {
            $this->Auth->allow(['confirmUser', 'prepareLogs']);
        }
    }

    /**
     * Processes log lifecycle triggers via HTTP request safely.
     *
     * @return \Cake\Http\Response
     */
    public function prepareLogs(): Response
    {
        $this->request->allowMethod(['post', 'get']);

        $logger = new CustomFileLog();
        $logger->log('info', 'Log cycle checked and recorded via Controller execution.');

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode([
                'status' => 'success',
                'message' => 'Log processed securely.'
            ]));
    }
}
