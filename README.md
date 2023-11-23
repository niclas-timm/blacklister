# 👮 Blacklister

Blacklist is a package for **Laravel** applications that enables you to validate email inputs against a blacklist.
You can block specific email addresses or entire domains.

## ⬇️ Installation

Install the package via composer:

```bash
composer require niclas-timm/blacklister
```

Next, run the following command to boostrap the necessary configuration:

```bash
php artisan blacklister:install
```

This does a few things:

1. It creates a `config/blacklister.php` file
2. It creates a file named `storage/framework/blacklist.json`. This is where you store your blacklist. You can rename
   and relocate this file later, if you want to (see "Configuration" section).

## 🤙 Usage

Blacklister uses a JSON file to store your blacklist. By default, this file is located
under `storage/framework/blacklist.json`, but you can configure it to your needs (see below).
In that file, two keys are required: `emails` and `domains`, which both expect arrays as their respective value. As the
names
suggest, you can use these to block individual
email addresses or entire domains.

### ✅ Using the validation

Using blacklister is super easy. Just add the `blacklist` keyword to your validation and you're ready to block! Like so:

```php
public function store(Request $request) {
  
  $validated = $request->validate([
        'email' => 'required|email|blacklist',
    ]);
    
}
```

### ➕ Adding new values to the blacklist

You can either paste the values **directly into the JSON file**, or use the following utility command:

```bash
php artisan blacklister:add {values} {--T|type}
```

| Argument | Description                                                                                        | Description                                       |
|----------|----------------------------------------------------------------------------------------------------|---------------------------------------------------|
| values   | The values you want to add to the blacklist. Multiple values are allowed.                          | `block@me.com`, `block@me.com leave-me@alone.com` |
| type     | The type of values you want to add to the blacklist (`emails` or `domains`). Defaults to `emails`. | `--type="emails"`,`--type="domains"`              |

For example:

```bash
# Block emails.
php artisan blacklister:add block@me.com leave-me@alone.com --type="emails"

# Block domain.
php artisan blacklister:add blockme.com --type="domains"
```

### 🔄 Updating cache

If the cache for Blacklister is enabled (see below), the values from the JSON file are cached. This means that you need
to update the cache whenever the blacklist JSON file changes.

If you use the `backlister:add` utility-command, this happens automatically. If you update the JSON file manually, you
need to execute the following command thereafter:

```bash
php artian blacklister:update-cache
```

> [!WARNING]  
> You must **run this command on your production system** as well in order for the changes to take effect there.
> If you update your blacklist frequently, it might make sense to add `php artian blacklister:update-cache` to your
> deployment script.

## ⚙️ Configuration

During the installation process, Blacklister creates the `config/blacklister.php` file for you, which defines how
blacklister behaves.

| Name               | Type    | Description                                                                                         | Example                                               |
|--------------------|---------|-----------------------------------------------------------------------------------------------------|-------------------------------------------------------|
| blacklist_path     | string  | The absolute path to your blacklist file.                                                           | `storage_path('framework/email_blacklist.json')`      |
| enable_cache       | boolean | If true, the content of the blacklist json file will be cached.                                     | `true`                                                |
| cache_key          | string  | Defines under which cache key the data will be cached (if cache is enabled).                        | `'blacklist'`                                         |
| cache_ttl          | int     | The time in minutes for how long the blacklist will be cached (if cache is enabled).                | `60 * 24 * 4` (4 days)                                |
| validation_message | string  | The validation message that will be displayed if the validation fails (is translatable by default). | `'The value is not allowed. Please use another one.'` |

You can check if your configuration is valid by executing the following command:

```bash
php artisan blacklister:verify
```

## 😎 How it works

Blacklister is actually really simple. If cache is disabled, if fetches the content from the JSON file on every
request and then checks if the individual email or the entire domain is on the blacklist.

If cache is enabled, Blacklister first tries to retrieve the blacklist from the cache. If it doesn't find it, it
loads the data from the JSON file and puts it into the cache, so it can be used the next time.