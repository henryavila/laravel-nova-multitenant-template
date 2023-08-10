# Laravel Nova Multi Tenant Template

This template provide a ready to start app with the following features and technologies
- [Laravel 10](https://laravel.com/docs/10.x)
- [Laravel nova 4](https://nova.laravel.com/docs)
  - With these packages (see de cods of each package):
    - https://github.com/laravel/nova-log-viewer
    - https://github.com/Muetze42/nova-menu
    - https://github.com/TitasGailius/nova-search-relations
    - https://github.com/spatie/browsershot
    - https://github.com/jbroadway/urlify
    - https://github.com/vinkla/hashids
  - With this customizations
    - Nova resources on `Resources` directory and files with `Resource` suffix
    - Allow custom ordering per Nova Resource using the property `$orderBy = ['name' => 'asc'];`
    - Custom Menu (with search and css customization)
    - Custom footer
    - Helpers
      - AppVersionHelper
      - FileHelperHelper
      - MetaTagsHelper (SEO)
      - TextHelper
      - Services
        - FileFromDbService (serve file loaded from DB) 
        - Wrapper to Browsershot
          ```php
          use App\Services\PdfConverter;
          
          public function __construct(public PdfConverter $pdfConverter)
          {
          }
            
            public function generatePdfFromViw()
            {
              // this will generate the pdf from a blade view and render it in the browser
              $this->pdfConverter
                  ->loadView('path.to-view', [
                      //... data to view
                  ])
                  ->inline('filename.pdf');  }
          ```
    - Traits
      - [UseHashId.php](app/Traits/UseHashId.php), for models (https://github.com/vinkla/hashids)

     
- [Vue 3](https://vuejs.org/guide) with (by [Laravel Breeze](https://laravel.com/docs/10.x/starter-kits#breeze-and-inertia) and [InertiJs](https://inertiajs.com/)) with
  - [Typescript](https://vuejs.org/guide/typescript/overview.html)
  - [TailwindCSS 3](https://tailwindcss.com/docs)
  - [Ziggy](https://github.com/tighten/ziggy)
- [Laravel Sanctum](https://laravel.com/docs/10.x/sanctum)
- `strict_types` in all pages
- Custom `laravel pint` rules
- Language pt_BR by default


----


- permission
- github action
- email tracking
- multi tentant 





# How to use

## Setup Laravel Nova
run the following code with your e-mail and nova licence key
 ```bash
 composer config http-basic.nova.laravel.com YOUR-NOVA-ACCOUNT-EMAIL@YOUR-DOMAIN.COM YOUR-LICENSE-KEY
 ```

update the `.env` file, setting your larva nova license key:
```dotenv
NOVA_LICENSE_KEY=PUT_YOUR_NOVA_LICENSE_KEY_HERE
```

And then run
```bash
composer update --prefer-dist
php artisan migrate
```

Create a user running
```bash
php artisan nova:user
```

For mor information about official Nova customization, consult the [Larave Nova docs](https://nova.laravel.com/docs/installation.html)

---
### Custom content
- By default, just **superAdmin** users can access nova outside environment.
  See [docs](https://nova.laravel.com/docs/installation.html#authorizing-access-to-nova) to change dhis logic. See [app/Models/User.php](app/Models/User.php) to change de `isSuperAdmin()` logic.

- To customize the menu, edit the files [app/nova/Menus/UserMenu.php](app/nova/Menus/UserMenu.php) and [app/nova/Menus/MainMenu.php](app/nova/Menus/MainMenu.php)

---


# Test
```bash
composer test
```
