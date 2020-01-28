## Welcome to LaraFrame Repo!
A powerful, state of the art, framework for laravel application.
### **Enable user authentication and management, use [Fe_Login](https://github.com/yu0307/Fe_Login)**
### **Enable user Role management, use [Fe_Roles](https://github.com/yu0307/Fe_Roles)**
### Let's collaborate!
Please send a email to me for bugs, feature suggestions,pull requests,etc... or even hang out :) [yu0307@gmail.com](mailto:yu0307@gmail.com)

### This package provides the following features:
    - Built-in UI framework for laravel application. Ideal for cloud system, admin control panel, CRM, general back-end interface and more.
    - Home page Widget support. Create custom widgets for users to choose at the front end.
    - List of stock widgets(weather,calendar,clock,etc) already built-in, instantly available for anyone who visits the home page.
    - Widget management. Re-arranging widgets at home page, add/remove widgets and change widget settings all at one place. 
    - Built-in templates for profile page, user setting page, etc. 
    - Built-in centralized control panel. Integrated with user management, role management, general settings, etc. Everything is added to the panel automatically.
    - Theme support. You can choose between themes like WordPress within the central control panel. 
    - Extendable Themes. You can create your own theme/designs and apply to the framework. Everything integrates gracefully. 
    - Revolutionary, BluePrints utility tool. Making building sites as easy as taking a survey. 
    - Built-in laravel commands for building components. Automatically generates controllers, models, migrations, etc. 
    - Extensive list of blade directives (forms, buttons,tables,etc) for fast interface development.
    - Built-in blade directives for common controls, take away the burden of writing repetitive codes.
    - Mobile responsive interface and menu design.
    - Built-in notification interface for users to view message/mails.
    - Menu Generator with support with icon, label, slug support.
    - Clean, elegant and modern design of front-end interface.

### Dependencies:
- Composer [Visit vendor](https://getcomposer.org/)
- Laravel 5+
- Compatible add-ons: [Fe_Login](https://github.com/yu0307/Fe_Login), [Fe_Roles](https://github.com/yu0307/Fe_Roles)

### Installation:

1. Please make sure composer is installed on your machine. For installation of composer, please visit [This Link](https://getcomposer.org/doc/00-intro.md)
2. Once composer is installed properly, please make sure Larave is up to date. 
3. Navigate to your project root directory
    ```
    composer require feiron/felaraframe
    ```
4. This package is going to publish several files to the following path
- config/felaraframe/
- public/feiron/felaraframe/
5. **Important!** This package is also going to perform several migrations. Please refer to the following changes and make backups of your tables if they are present. 
    ```
    Schema to be Created/Modified:
    [lf_notes]:
    id bigint(20) UN AI PK 
    subject varchar(220) 
    notes text 
    notable_id varchar(36) 
    notable_type varchar(50) 
    created_at timestamp 
    updated_at timestamp
    ------------------------------------------
    [user_widget_layout]:
    id bigint(20) UN AI PK 
    layoutable_id varchar(36) 
    layoutable_type varchar(36) 
    widget_name varchar(225) 
    settings text 
    order int(11) 
    created_at timestamp 
    updated_at timestamp
    ------------------------------------------
    [lf_mail]:
    id bigint(20) UN AI PK 
    sender int(11) 
    recipient int(11) 
    subject varchar(220) 
    contents text 
    remarks varchar(191) 
    created_at timestamp 
    updated_at timestamp
    ------------------------------------------
    [lf_site_metainfo]:
    id bigint(20) UN AI PK 
    meta_name varchar(225) 
    meta_value text 
    created_at timestamp 
    updated_at timestamp
    ```
**Note**: During migration, if you encounter error showing "Specified key was too long"
This was due to MySQL version being older than 5.7.7, if you don't wish to upgrade MySQL server, consider the following.

Within your AppServiceProvider 

    ```
    use Illuminate\Support\Facades\Schema;

    /**
    * Bootstrap any application services.
    *
    * @return void
    */

    public function boot()
    {
        Schema::defaultStringLength(191);
    }
    ```

Further reading on this could be found at [This Link](https://laravel.com/docs/master/migrations#creating-indexes)

### Basic Usage:
**For details of how to use this framework. Please head over to the [WiKi](https://github.com/yu0307/LaraFrame/wiki) page of this Repo.**
There are a lot of features packed into this package and I will be updating the [WiKi Page](https://github.com/yu0307/LaraFrame/wiki) frequently, please stay tuned.

### Notes:
- This package does not come with user support nor with role management. 
- For these functions, please use [Fe_Login](https://github.com/yu0307/Fe_Login) and [Fe_Roles](https://github.com/yu0307/Fe_Roles) which were specifically developed to work with this framework. 