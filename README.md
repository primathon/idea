# Laravel Rapid Idea Deployment

**Current Version:** 0.0.8 (mostly stable)

I got sick of having to do the same thing over and over and over when writing code:

- Come up with an idea
- Create a migration to handle the database table
- Create a model and define validation rules
- Create a restful controller and the seven required methods
- Add the necessary routes to my routes.php file (okay, this one is pretty easy)
- Create the index, create, show, and edit model-bound views that the controller uses
- Create a language file that holds the form label text (you're using i18n, right?)
- Add any and all placeholder values to each form element

And when I changed something?

- Preserve the data so I can re-migrate and re-seed without losing anything
- Automate as much of the boilerplate code change as possible

I found a few tools that helped me quite a bit with these steps:

- [Laravel 4 Generators](https://github.com/JeffreyWay/Laravel-4-Generators) - Jeffrey Way's fantastic Generators package for Laravel 4
- [Larry Four Generators](https://github.com/XCMer/larry-four-generator) - An alternate Generator package that references an external data file
- [Ardent for Laravel 4](https://github.com/laravelbook/ardent) - Self-validating smart models for Laravel 4's Eloquent ORM

Credit to their respective authors; these packages have helped me **immmensely** over the last few months. Also, I have -ahem- "liberated" quite a few ideas and code blocks from each package.


### This is not an everything machine

I would like to specifically point out that this is not a 'one-size-fits-all' approach to building web applications. This is simply a way to get from idea to working prototype in the shortest amount of time.
Given that this (intentionally) does not handle advanced data concepts like pivot tables and polymorphic relations, you should be using this to bang out a concept and modify the output to suit your needs.


## Installation

Eventually, I will put this on <a href="https://packagist.org/packages/primathon/idea">Packagist</a>, but that time is not yet here.

You'll probably follow these steps:

- Put the following in your composer.json: `"primathon/idea": "1.*"`
- Run `composer update`
- Add `'Primathon\Idea\IdeaServiceProvider'` to the `providers` array of `app/config/app.php`


## Example Idea File

	// Model definition
    User users; timestamps; softDeletes; views "admin/users"

		// Field definitions
        id increments
		username string 50; label "Enter a username"; default "no username set"; placeholder "username"; rules "required|alpha|unique"; nullable
        password string 64; label "Enter your password"; placeholder "password"; rules "required"
        email string 250; label "User email address"; placeholder "email@domain.com"; rules "required|unique|email"
        type enum admin, moderator, user; rules "required"
		active boolean; default 0


## Usage

Once the package has been installed, you can access the commands via `artisan`, available under the `idea` namespace.

The package supports the following commands:

- `php artisan idea:controller <idea_file>`
- `php artisan idea:lang <idea_file>`
- `php artisan idea:migration <idea_file>`
- `php artisan idea:model <idea_file>`
- `php artisan idea:routes <idea_file>` // not yet complete
- `php artisan idea:seed <idea_file>` // not yet complete
- `php artisan idea:views <idea_file>` // not yet complete

As you may have figured out, `<idea_file>` is a required input for each command. You have to provide a filename that exists at the root of your Laravel 4 installation, where `artisan` itself resides.

You cannot provide absolute paths for the Idea file yet. If you're providing a relative path instead of a filename, then it is relative to Laravel's root directory (or basepath).


### Idea file Syntax

Your model definition **must** have no whitespace in front of it. This is not optional.

Your field definitions **must** have a tab or spaces in front of each one. This is not optional.

Most everything is semicolon-delimited. The final semicolon is optional.

Blank lines and comments (beginning with `//`) will be ignored.


### Model definition

An example model definition looks like this:

	ModelName model_table; timestamps; softDeletes; route "modelroute.here"; views "viewspath/goes/here"

Note that the model definition **must** have no whitespace in front of it.

Breaking it down by segment:

- `ModelName model_table` - The Model name (used in Controllers, Migrations, etc) and associated database table
- `timestamps` - adds a `timestamps` entry to the Migration file.
- `softDeletes` - adds a `softDeletes` entry to the Migration file.
- `route "modelroute.here"` - the routes path that this Model should reference. Used in the `routes.php` file and the Controller. Defaults to the value of `model_table`.
- `views "viewspath/goes/here"` - the views path. Your generated views will be placed in this folder (relative to app/views), and your Controller will load views from here. Defaults to the value of `model_table`.

Note that the routes and views path can be delimited by a period *or* a slash -- they are internally converted to their proper representations when your files are generated.


### Field definition

After you define a model, you need to define fields for it. The field definitions all operate off the following pattern:

	fieldname fieldtype parameters; default "default value"; placeholder "value here"; rules "numeric|required|etc"; label "This is the fieldname form label"

- `fieldname` - This is (surprisingly) the column name of the field.
- `fieldtype` - Field type. For a full list of supported types, see below.
- `parameters` - Parameters for the field type. Often optional. Examples are string max lengths, enum values, etc.
- `default` - The default value for the field if nothing is specified (optional).
- `label` - The label associated with this field, referenced in the Lang file (optional) and used in the View forms.
- `placeholder` - The placeholder value for the field (optional). Only applies to text input (strings, integers, etc).
- `rules` - Pipe-separated validation rules to apply to this field (optional), used when saving data to the database. Leverages the Ardent Model validation.
- `nullable` - Indicates that the field should allow NULL values (optional)
- `unsigned` - Indicates an unsigned numeric type; only integer, bigInteger, smallInteger, tinyInteger are supported (optional)

If it makes you feel better, you can add `timestamps` and `softDeletes` as field definitions instead of in the Model. I may change this at some point.


#### Field Types ####

    increments
    string
    integer
    bigInteger
    smallInteger
	tinyInteger
    float
	double
    decimal
    boolean
    date
    dateTime
    time
    timestamp (not timestamp**s**)
    text
	longText
	mediumText
    binary
    enum

The `enum` field type utilizes a comma-separated list of values. These can optionally be enclosed in quote marks:

    type enum "admin", "moderator", "user", "unregistered"
    type enum admin, moderator, user, unregistered


### Assumptions ###

- Adding `timestamps` in your Model definition will include the `created_at` and `updated_at` columns in your table. Unlike Laravel, this is `false` by default.
- The `softDeletes` value is false by default. Adding it to your Model definition will include it in your generated Migrations.
- In your Model definition, the `route` and `views` options will default to the `table_name` value.
- Laravel creates all fieldtypes as non-nullable (at least using MySQL) unless otherwise noted;
- Indexes will only be assigned on the primary 'increments' key; all other indexes (either unique or basic) are not supported. Add them in yourself.


## Templates ##

By default, the templates are "Bootstrappy", meaning that they leverage the Boostrap 3 framework.

If you want to override the included templates, place them in `idea/templates` and use the following names:

	// View files
	view.create
	view.edit
	view.index
	view.show

	// Field templates
	field.checkbox
	field.date
	field.hidden
	field.select
	field.text
	field.text-sm
	field.textarea


## Error handling

I have tried to implement a robust error handling mechanism. The package will handle the following 'oops':

- Syntax errors in the Idea file
- Invalid field types and modifiers
- Inability to write files to the intended path
- Some other stuff that I'm forgetting


## Testing

Filed under the topic of "something I know that I absolutely need to be doing but am absolutely no good at (yet)".

I implore anyone who thinks they can point me in the right direction on this to shoot me an email.

