# ShibbolethAuthLimeSurvey

This plugin enables you to use [Shibboleth](https://www.shibboleth.net/) as authentication mechanism in [LimeSurvey](https://www.limesurvey.org).

The current changelog can be found [here](CHANGELOG.md).

This plugin was tested with the following versions of LimeSurvey:

* [5.6.68+240625](https://github.com/LimeSurvey/LimeSurvey/tree/5.6.68%2B240625)
* [6.5.17+240715](https://github.com/LimeSurvey/LimeSurvey/tree/6.5.17%2B240715)

**LimeSurvey 5+ Shibboleth auth plugin**

**LimeSurvey: http://www.limesurvey.org/**

## PREREQUISITES
* Running installation of LimeSurvey 5 or 6
* libapache2-mod-shib2 -> Running Shibboleth SP
* git

## INSTALL PLUGIN

In the following example the LimeSurvey working directory is /var/www/limesurvey

To install this plugin you have to create a folder "ShibbolethAuthLime" into folder /plugins/ of your LimeSurvey installation and copy into that folder the file "ShibbolethAuth.php":

```bash
cd /var/www/limesurvey/plugins
mkdir ShibbolethAuthLime
cd ShibbolethAuthLime
git clone https://github.com/stevleibelt/ShibbolethAuthLimeSurvey
```
## ACTIVATE PLUGIN FROM ADMIN PANEL

Now you can activate and configure the new installed plugin

## CONFIGURE APACHE2 FOR SHIBBOLETH AUTHENTICATION

You have two alternatives:

**Protect frontend and admin panel with Shibboleth**
To protect frontend and admin panel you can add the following to apache2 configuration:
```bash
   <Location />
             AuthType shibboleth
             ShibRequireSession On
             require valid-user
   </Location>
```

**Protect only admin panel with Shibboleth**
With this method you will be able to protect only the admin panel, add the following to apache2 configuration:
```bash
   <Location /admin>
             AuthType shibboleth
             ShibRequireSession On
             require valid-user
   </Location>

   <Location />
             AuthType shibboleth
             ShibRequestSetting requireSession false
             ShibUseHeaders On
             Require shibboleth
  </Location>
```

## Links

* [atlet's original (?) code for LimeSurvey version 3.4+](https://github.com/atlet/LimeSurvey-ShibbolethAuth)
* [composer JSON schema documentation](https://getcomposer.org/doc/04-schema.md)
* [leandrobhbr's adaptation for LimeSurvey version 4](https://github.com/leandrobhbr/ShibbolethAuthLimeSurvey)
* [PHP supported versions](https://www.php.net/supported-versions.php)
* [stevleibelt's adaptation for LimeSurvey version 5 and 6](https://github.com/stevleibelt/ShibbolethAuthLimeSurvey)

## Contributers

* [atlet](https://github.com/atlet)
* [leandrobhbr](https://github.com/leandrobhbr)
* [stevleibelt](https://github.com/stevleibelt)

