<?php
/**
 * Author: vita2333
 * Date: 2019/4/9
 */

namespace AdminLog;

use AdminLog\models\AdminLog;
use Yii;
use yii\base\Action;
use yii\base\Behavior;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\base\Module;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\Controller;

class AdminLogBehavior extends Behavior
{

    /**
     * @var \yii\web\Controller
     */
    public $owner;

    /**
     *  public function init()
     * {
     *      parent::init();
     *      $this->enableAdminLog = true;
     *      $this->adminLogOnly = ['index','list'];
     *      $this->adminLogExcept = ['list'];
     * }
     */
    public $enableAdminLog = true;
    public $adminLogOnly   = [];
    public $adminLogExcept = [];

    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'beforeAction',
        ];
    }

    public function beforeAction($event)
    {
        if ($this->isActive($this->owner->action)) {
            Event::on(ActiveRecord::class, BaseActiveRecord::EVENT_AFTER_INSERT, [$this, 'afterModelChanged']);
            Event::on(ActiveRecord::class, BaseActiveRecord::EVENT_BEFORE_UPDATE, [$this, 'afterModelChanged']);
            Event::on(ActiveRecord::class, BaseActiveRecord::EVENT_AFTER_DELETE, [$this, 'afterModelChanged']);
        }
    }

    protected function isActive(Action $action): bool
    {
        if ( ! $this->enableAdminLog) {
            return false;
        }
        $id = $this->getActionId($action);

        if (empty($this->adminLogOnly)) {
            $onlyMatch = true;
        } else {
            $onlyMatch = false;
            foreach ($this->adminLogOnly as $pattern) {
                if (StringHelper::matchWildcard($pattern, $id)) {
                    $onlyMatch = true;
                    break;
                }
            }
        }

        $exceptMatch = false;
        foreach ($this->adminLogExcept as $pattern) {
            if (StringHelper::matchWildcard($pattern, $id)) {
                $exceptMatch = true;
                break;
            }
        }

        return ! $exceptMatch && $onlyMatch;
    }

    protected function getActionId(Action $action)
    {
        if ($this->owner instanceof Module) {
            $mid = $this->owner->getUniqueId();
            $id  = $action->getUniqueId();
            if ($mid !== '' && strpos($id, $mid) === 0) {
                $id = substr($id, strlen($mid) + 1);
            }
        } else {
            $id = $action->id;
        }

        return $id;
    }

    public function afterModelChanged($event)
    {
        /** @var ActiveRecord $sender */
        $sender = $event->sender;
        if ($sender instanceof AdminLog) {
            return true;
        }

        if ($event->name === ActiveRecord::EVENT_AFTER_INSERT) {
            $type = EnumLogType::TYPE_INSERT;
        } elseif ($event->name === ActiveRecord::EVENT_AFTER_UPDATE) {
            $type = EnumLogType::TYPE_INSERT;
        } elseif ($event->name === ActiveRecord::EVENT_BEFORE_UPDATE) {
            $type = EnumLogType::TYPE_INSERT;
        }

        $info = $this->getUserInfo();

        $tableName = $sender::tableName();
        $tableName = str_replace(['{{%', '}}'], '', $tableName);

        $log = new AdminLog([
            'user_id'    => $info[0],
            'username'   => $info[1],
            'type'       => $type,
            'table_name' => $tableName,
            'route'      => Url::to(),
            'ip'         => Yii::$app->request->userIP,
        ]);

        $typeText = EnumLogType::getText($log->type);
        $desc     = $this->getChangedAttrDesc($event);
        $tablePk  = $sender->getPrimaryKey();

        $log->description = "{$log->username}{$typeText}了表{$log->table_name}:[{$tablePk}]的 {$desc}";
        $log->save();

        return $log;
    }

    protected function getUserInfo()
    {
        if (Yii::$app->has('user')) {
            /** @var \yii\web\User $user */
            $user = Yii::$app->get('user');
            /** @var \AdminLog\IdentityInterface $identity */
            if ($identity = $user->getIdentity()) {
                $uid   = $identity->getId();
                $uname = $identity->getUsername();
            } else {
                $uid   = '0';
                $uname = '游客';
            }

            return [$uid, $uname];
        }
        throw new InvalidConfigException('user 模块未配置');
    }

    protected function getChangedAttrDesc($event)
    {
        if ( ! empty($event->changedAttributes)) {
            $desc = '';
            foreach ($event->changedAttributes as $name => $value) {
                $desc .= $name . ' : ' . $value . '=>' . $event->sender->getAttribute($name) . ',';
            }
            $desc = substr($desc, 0, -1);

            return $desc;
        }

        return '';
    }

}