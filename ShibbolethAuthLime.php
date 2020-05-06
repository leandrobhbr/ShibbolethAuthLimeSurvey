<?php

# Only for test
#$_SERVER['Shib-Person-UID']="leandocampos";
#$_SERVER['Shib-InetOrgPerson-givenName']="Leandro Campos";
#$_SERVER['Shib-Person-surname']="Leandro Campos";
#$_SERVER['Ship-Person-Mail']="leandrocampos@ufmg.br";


class ShibbolethAuthLime extends AuthPluginBase {

    protected $storage = 'DbStorage';
    static protected $description = 'Shibboleth authentication';
    static protected $name = 'ShibbolethAuthLime';
    public $atributi = '';
    public $mail = '';
    public $displayName = '';
    protected $settings = array(
			'authuserid' => array(
            'type' => 'string',
            'label' => 'Shibboleth attribute of User ID (eg. eduPersonPrincipalName)',
            'default' => 'eduPersonPrincipalName',
        ),
            'authusergivenName' => array(
            'type' => 'string',
            'label' => 'Shibboleth attribute of User first name (eg. givenName)',
            'default' => 'givenName',
        ),
            'authusergivenSurname' => array(
            'type' => 'string',
            'label' => 'Shibboleth attribute of User surname (eg. sn)',
            'default' => 'sn',
        ),
            'mailattribute' => array(
            'type' => 'string',
            'label' => 'Shibboleth attribute of User email address (eg. mail)',
            'default' => 'mail',
		),
            'logoffurl' => array(
            'type' => 'string',
            'label' => 'Redirecting url after LogOff',
            'default' => 'https://my.example.com/Account/Logoff',
		),
            'is_default' => array(
            'type' => 'checkbox',
            'label' => 'Check to make default authentication method (this disable Default LimeSurvey authentification by database)',
            'default' => false,
        ),
            'autocreateuser' => array(
            'type' => 'checkbox',
            'label' => 'Automatically create user if not exists',
            'default' => true,
        ),
            'permission_create_survey' => array(
            'type' => 'checkbox',
            'label' => 'Permission create survey',
            'default' => false,
        )
    );

    public function init(){

        $this->subscribe('beforeLogin','beforeLogin');
        $this->subscribe('newUserSession','newUserSession');
		$this->subscribe('afterLogout','afterLogout');
    }


    public function beforeLogin() {
		// Do nothing if this user is not ShibbolethAuth type
        $identity = $this->getEvent()->get('identity');
        $pluginType = $identity->plugin;
        #$pluginType = 'ShibbolethAuth'; // force test
		if ($pluginType != 'ShibbolethAuth')
		{
            $msg="identity->plugin: need be ShibbolethAuth. Is how {$identity->plugin}";
            echo "<script>alert('$msg')</script>";
            return;
        }

		$authuserid = $this->get('authuserid');
		$authusergivenName = $this->get('authusergivenName');
		$authusergivenSurname = $this->get('authusergivenSurname');
		$mailattribute = $this->get('mailattribute');

		if (!empty($authuserid) && isset($_SERVER[$authuserid]))
		{
			$sUser=$_SERVER[$authuserid];

			// Possible mapping of users to a different identifier
			$aUserMappings=$this->api->getConfigKey('auth_webserver_user_map', array());
            if (isset($aUserMappings[$sUser]))
            {
               $sUser = $aUserMappings[$sUser];
            }

			// If is set "autocreateuser" option then create the new user
            if($this->get('autocreateuser',null,null,$this->settings['autocreateuser']['default']))
            {
                $this->setUsername($sUser);
				$this->displayName = $_SERVER[$authusergivenName].' '.$_SERVER[$authusergivenSurname];

				if($_SERVER[$mailattribute] && $_SERVER[$mailattribute] != '')
				{
					$this->mail = $_SERVER[$mailattribute];
				}
				else $this->mail = 'noreply@my.example.com';

                $this->setAuthPlugin(); // This plugin handles authentication, halt further execution of auth plugins
            }
            elseif($this->get('is_default',null,null,$this->settings['is_default']['default']))
            {
                throw new CHttpException(401,'Wrong credentials for LimeSurvey administration: "' . $sUser . '".');
            }
		}
    }

    public function newUserSession() {
        $sUser = $this->getUserName();
        $oUser = $this->api->getUserByName($sUser);

        if (is_null($oUser)) {
            // Create new user

            $name = $sUser;
            $email = $this->mail;
            // generate aleatory password
            $password = date('YmdHis').rand(0,1000);
            $oUser = new User;
            $oUser->users_name = $name;
            $oUser->full_name = $name;
            $oUser->email = $email;
            $oUser->parent_id = 1;
            $oUser->created = date('Y-m-d H:i:s');
            $oUser->modified = date('Y-m-d H:i:s');
            $oUser->password = password_hash($password, PASSWORD_DEFAULT);

            if ($oUser->save()) {

                if ($this->get('permission_create_survey', null, null, false)) {
                    $permission = new Permission;
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
                return;
            } else {
                $this->setAuthFailure(self::ERROR_USERNAME_INVALID);
                return;
            }

            return;
        } else { // The user alredy exists
            $this->setAuthSuccess($oUser);
        }
    }

	public function afterLogout()
    {
		$logoffurl = $this->get('logoffurl');

		if (!empty($logoffurl))
		{
			// Logout Shibboleth
			header("Location: " . $logoffurl);
			die();
		}
    }

}


