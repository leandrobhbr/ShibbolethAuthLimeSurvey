<?php

# Only for test - needs to be the same as the plugin settings page in the administrative
#$_SERVER['Shib-Person-UID']="<UID>";
#$_SERVER['Shib-InetOrgPerson-givenName']="<givenName>";
#$_SERVER['Shib-Person-surname']="<SN>";
#$_SERVER['Shib-Person-Mail']="<MAIL>";
# end test

class ShibbolethAuthLime extends AuthPluginBase
{
    protected $storage = 'DbStorage';
    protected static $description = 'Shibboleth authentication';
    protected static $name = 'ShibbolethAuthLime';
    public $atributi = '';
    public $mail = '';
    public $displayName = '';
    protected $settings = ['authuserid' => ['type' => 'string', 'label' => 'Shibboleth attribute of User ID (eg. eduPersonPrincipalName)', 'default' => 'eduPersonPrincipalName'], 'authusergivenName' => ['type' => 'string', 'label' => 'Shibboleth attribute of User first name (eg. givenName)', 'default' => 'givenName'], 'authusergivenSurname' => ['type' => 'string', 'label' => 'Shibboleth attribute of User surname (eg. sn)', 'default' => 'sn'], 'mailattribute' => ['type' => 'string', 'label' => 'Shibboleth attribute of User email address (eg. mail)', 'default' => 'mail'], 'logoffurl' => [
        'type' => 'string',
        'label' => 'Redirecting url after LogOff',
        //'default' => 'https://my.example.com/Account/Logoff',
        'default' => 'https://www.unibg.it',
    ], 'is_default' => ['type' => 'checkbox', 'label' => 'Check to make default authentication method (this disable Default LimeSurvey authentification by database)', 'default' => false], 'autocreateuser' => ['type' => 'checkbox', 'label' => 'Automatically create user if not exists', 'default' => true], 'permission_create_survey' => ['type' => 'checkbox', 'label' => 'Permission create survey', 'default' => false]];

    public function init()
    {
        /* only test
        $fd = fopen("/var/www/vhost/sia.unibg.it/a.a","w");
        fwrite($fd,"PASSATO NELLA INIT\n");
        fclose($fd);
        */

        $this->subscribe('beforeLogin', 'beforeLogin');
        $this->subscribe('newUserSession', 'newUserSession');
        $this->subscribe('afterLogout', 'afterLogout');
    }

    public function beforeLogin()
    {

        $authuserid = $this->get('authuserid');
        $authusergivenName = $this->get('authusergivenName');
        $authusergivenSurname = $this->get('authusergivenSurname');
        $mailattribute = $this->get('mailattribute');
        $sUser = $this->getUserName();

        if(empty($authuserid) && empty($_SERVER[$authuserid])) {
            return;
        } // not login by shiboleth

        // Possible mapping of users to a different identifier
        $aUserMappings = $this->api->getConfigKey('auth_webserver_user_map', []);
        $sUser = $aUserMappings[$sUser] ?? $_SERVER[$authuserid];

        $autocreateuser = ($autocreateuser === null || trim($autocreateuser) === '') ? 'autocreateuser' : $autocreateuser ;
        /* autocreate TRUE */
        if($this->get($autocreateuser, null, null, $this->settings['autocreateuser']['default'])) {
            $this->setUsername($sUser);
            $this->displayName = $_SERVER[$authusergivenName].' '.$_SERVER[$authusergivenSurname];
            $this->mail = ($_SERVER[$mailattribute] && $_SERVER[$mailattribute] != '') ? $_SERVER[$mailattribute] : 'noreply@unibg.it';
            $this->setAuthPlugin(); // This plugin handles authentication, halt further execution of auth plugins
        } elseif($this->get('is_default', null, null, $this->settings['is_default']['default'])) {
            throw new CHttpException(401, 'Wrong credentials for LimeSurvey administration: "' . $sUser . '".');

            /* autocreate FALSE */
        } else {
            $this->setUsername($sUser);
            $this->displayName = $_SERVER[$authusergivenName].' '.$_SERVER[$authusergivenSurname];
            $this->setAuthPlugin(); // This plugin handles authentication, halt further execution of auth plugins
        }

    }

    public function newUserSession()
    {

        $sUser = $this->getUserName();
        $oUser = $this->api->getUserByName($sUser);
        /* only test
        $fd = fopen("/var/www/vhost/sia.unibg.it/a.a","a");
        $LogEntry1 = $oUser;
        fwrite($fd,"oUser = $LogEntry1\n");
        fclose($fd);
        */
        // The user alredy exists - can login with success
        if(!empty($oUser)) {
            $this->setAuthSuccess($oUser);
            return;
        }

        // OR Create new user
        $name = $sUser;
        $email = $this->mail;
        // generate aleatory password
        $password = date('YmdHis').random_int(0, 1000);
        $oUser = new User();
        $oUser->users_name = $name;
        $oUser->full_name = $this->displayName;
        $oUser->email = $email;
        $oUser->parent_id = 1;
        $oUser->created = date('Y-m-d H:i:s');
        $oUser->modified = date('Y-m-d H:i:s');
        $oUser->password = password_hash($password, PASSWORD_DEFAULT);
        if (!$oUser->save()) {
            $this->setAuthFailure(self::ERROR_USERNAME_INVALID);
            return;
        }
        // need permission? it is settings of plugin - by admin page
        if ($this->get('permission_create_survey', null, null, false)) {
            $permission = new Permission();
            $permission->entity_id = 0;
            $permission->entity = 'global';
            $permission->uid = $oUser->uid;
            $permission->permission = 'surveys';
            $permission->create_p = 1;
            $permission->read_p = 0;
            $permission->update_p = 0;
            $permission->delete_p = 0;
            $permission->import_p = 0;
            $permission->export_p = 0;
            $permission->save();
        }
        $this->setAuthSuccess($oUser);
    }

    public function afterLogout()
    {

        $logoffurl = $this->get('logoffurl');
        if (!empty($logoffurl)) {
            // Logout Shibboleth
            header("Location: " . $logoffurl);
            die();
        }
    }
}
