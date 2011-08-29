ZFP - Zend Framework Extensions
===============================

Collection of my Zend Framework (1.x) extensions for common use cases.

Domain Layer / Data Access
--------------------------
In many cases a high complex domain layer is too difficult to handle in a ZF application. So I use an approach with
a moderate complexity that uses a data mapper to communicate between domain models and database tables.
A mapper creates domain models (entities or collections). Each mapper belongs to one data access object (table gateway).
The domain models can utilize any mapper to lazy load sub-models. (Although this violates a clean domain layer,
I like this approach because it's easy to know where to get my data without the need of too many objects.)

Pet_Domain_xx - domain model, classes for model entities and collections
Pet_Model_Mapper - data mapper between table gateway and domain models
Pet_Db_Table - table gateway

Maintenance Mode
----------------
Sometimes it useful to route any request to a specific controller/action to show a maintenance page.
All configuration is done in application.ini. It's possible to get normal access to the site with a special POST/GET param.

Pet_Application_Resource_Maintenance - application resource to activate and configure a maintenance mode
Pet_Controller_Plugin_Maintenance - controller plugin to change routing

Configuraton example for application.ini 
    ; Maintenance
    resources.maintenance.enabled = false
    resources.maintenance.pluginname = "Pet_Controller_Plugin_Maintenance"
    resources.maintenance.target.action = 'index'
    resources.maintenance.target.controller = 'maintenance'
    resources.maintenance.target.module = 'default'
    resources.maintenance.accessparam = 'skipmaintenance'

Settings Resource
-----------------
Settings is a resource module to access any settings that can be set via application.ini or a dedicated settings file (any Zend_Config type).
All settings are available by use of regsitry key 'settings'.

Configuraton example for application.ini
    ; Settings
    resources.settings.thumb.path = '/my/path'
    resources.settings.thumb.cachetime = 6400
    resources.settings.email = 'my@email.com'

or

    ; Settings
    resources.settings = APPLICATION_PATH "/configs/settings.ini"