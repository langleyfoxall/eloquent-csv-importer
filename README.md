# 💾 Eloquent CSV Importer

Eloquent CSV Importer helps create and store columns maps to enable the easy conversion of CSV data to Eloquent models

<p align="center">
    <img src="assets/images/example-code.png">
</p>

## Installation

Eloquent CSV Importer can be easily installed using Composer. Just run the following command from the root of your project.

```
composer require langleyfoxall/eloquent-csv-importer
```

The service provider is to set to be auto discovered in newer versions of Laravel - in older versions you will have to manuallly register it in `config/app.php`

```
LangleyFoxall\EloquentCSVImporter\EloquentCSVImporterServiceProvider::class
```

After this, publish the vendor files to copy the CSV definitions migrations to your migrations folder.

```
php artisan vendor:publish
```

## Documentation

Documentation coming soon
