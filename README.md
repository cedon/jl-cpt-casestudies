# JL CPT Fitness Case Study

[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg?style=plastic)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html)

JL CPT Fitness Case Study is a WordPress plugin the creates a Case Study custom post type to help Fitness professionals.

This plugin is is based on the custom post type helper class described by Gils Jorissen in the [Custom Post Type Helper Class](https://code.tutsplus.com/articles/custom-post-type-helper-class--wp-25104) and then modified to accept a different configuration of parameters to define meta box fields.

Note: This plugin is not 100% compatible with the upcoming [Gutenberg](https://github.com/WordPress/gutenberg) editor. While a lot of it will work, instances of `wp_editor()` for meta fields will automatically force WordPress to fall back to the Classic Editor to avoid issues. Full Gutenberg compatibility is a goal of a later version of this project.

## Class Use
This plugin is dependant on the `jl-custom-post-type` class that allows users to create a custom post type, custom taxonomy, and meta boxes. It contains several default values for labels and arguments that can be overridden.

### Creating a Custom Post Type
Syntax:
```php
$new_cpt = new JL_CustomPostType( 'CPT Name', $args, $labels );
```

A custom post type can be created by simply creating a new instance of the object and passing it a string for the custom post type name. By default, the class will have certain arguments and labels the will work for the majority of users. For example, if one wanted to create a custom post type for `Book Reviews` you could create it with the following:

```php
$new_cpt = new JL_CustomPostType( 'Book Review' );
```

[Arguments](https://developer.wordpress.org/reference/functions/register_post_type/#parameters) and [Labels](https://developer.wordpress.org/reference/functions/get_post_type_labels/#description) can be added by passing arrays after the CPT name:

```php
// $new_cpt = 
$new_cpt = new JL_CustomPostType( 'Book Review', 
                                  array( 'show_in_rest' => 'false' ), 
                                  array( 'name' => 'Reviews' )
                                 );
```

### Setting the Post Key
In order to help namespace, a post key is created by the class to be used to preprend certain variables. By default the class will create one from the first eight letters in the name of the custom post type. So the example of `Book Review` would be `bookrevi`.

This can be overridden with the `set_post_key()` method:

```php
$new_cpt->set_post_key( 'reviews' );
```

### Creating a Custom Taxonomy
Syntax:

```php
$new_cpt->add_taxonomy( 'Name', $args, $labels );
```

The `add_taxonomy()` method will create a custom taxonomy for the new custom post type and can be created with only a name. So in the case of the example `Book Review` custom post type if a taxonomy of `Genre` is needed:

```php
$new_cpt->add_taxonomy( 'Genre' );
``` 
 
 Like the creation of the post type itself, this method has several defaults for labels and [arguments](https://developer.wordpress.org/reference/functions/register_taxonomy/#parameters) that it passes to WordPress that can be overridden. One of those defaults is a non-hierarchical structure so new taxonomies will behave like [tags](http://www.wpbeginner.com/glossary/tag/). If they need to act like categories then the `hierarchical` argument can be passed:
 
 ```php
$new_cpt->add_taxonomy( 'Genre', array( 'hierarchical' => true ) );
```

Note: The class will first check to see if the taxonomy is already registered. In the event that it is, then it will just associate the taxonomy with the custom post type.

### Creating Meta Boxes & Meta Fields

Syntax:

```php
$new_cpt->add_meta_box(
    'Meta Box Title',
    'array(
        'Meta Field Name' => array(
            'type' => 'field type',
            // Other Keys Dependent on 'type'
            ),
            'break' => true,
        ),
    ),
);
```

This method will create both a meta box and the fields within it based on the first argument being the desired title of the Meta Box and then an array defining all of the fields.

The `type` key in the array of fields tells the class what kind of meta field that is to be created. Currently the only valid values are `text`, `select`, `checkbox`, `radio`, `textarea`, `wpeditor`, and `attachment`.

There is also an optional key of `break`, which accepts a boolean value  of `true` to insert a `br` element after the field.

#### Creating a Text Meta Field

A text meta field is created by setting the `type` key in the field array to `text`. This type also will utilize an optional key of `attributes` which is an array of valid HTML attributes for an `input` element such as `maxlength` or `required`. So, using the example of of the `Book Review` custom post type, a meta field of `Book Title` with a maximum length of `64` can be created as follows:

```php
$new_cpt->add_meta_box(
    'Book Information',
    'array(
        'Book Title' => array(
            'type' => 'text',
            'attributes' => array(
                'maxlength' => 64,
            ),
        ),
    ),
);
```

#### Creating a Select Meta Field

A drop down list of options can be created by setting the meta field `type` to `select`. This requires the use of the key `select_options` which is an array that contains all of the options to be added. To add a `select` meta field for `genre`:

```php
$new_cpt->add_meta_box(
    'Book Information',
    'array(
        'Genre' => array(
            'type' => 'select',
            'select_options' => array(
                'Literature',
                'Horror',
                'Fantasy',
                'Romance',
                'Sci-Fi',
            ),
        ),
    ),
);
```

#### Creating a Radio Button Meta Field

A radio group meta field is defined in a very similar way as Select field. Instead of `select_options`, it uses a `radio_options` key to define all the individual radio buttons to create. So, in order to make the genre selection a radio group:

```php
$new_cpt->add_meta_box(
    'Book Information',
    'array(
        'Genre' => array(
            'type' => 'radio',
            'radio_options' => array(
                'Literature',
                'Horror',
                'Fantasy',
                'Romance',
                'Sci-Fi',
            ),
        ),
    ),
);
```

#### Creating a Text Area Meta Field

The `textarea` meta field also accepts an optional `attributes` key with an array of valid HTML5 attributes:

```php
$new_cpt->add_meta_box(
    'Plot Summary',
    'array(
        'Summary' => array(
            'type' => 'textarea',
            'attributes' => array(
                'rows' => 5,
                'cols' => 80,
            ),
        ),
    ),
);
```

#### Creating a `wp_editor()` Meta Field

When you need the full WordPress editor for a meta field, this can be created by setting `'type'` to `'wpeditor'`. An optional key of `'wpeditor_options'` can also be used containing an array of [arguments](https://codex.wordpress.org/Function_Reference/wp_editor#Arguments) for `wp_editor()`. The `textarea_name` argument is already defined as the name of the field.

```php
$new_cpt->add_meta_box(
    'Plot Summary',
    'array(
        'Summary' => array(
            'type' => 'wpeditor',
            'wpeditor_options' => array(
                'textarea_rows' => 5,
                'media_buttons' => false,
            ),
        ),
    ),
);
```

Note: As mentioned above, use of `wp_editor()` makes the plugin incompatible with the upcoming Gutenberg editor so WordPress will fall back to the classic editor.

#### Creating an Attachment Upload Meta Field

```php
$new_cpt->add_meta_box(
        	'Book Covers',
        	array(
        		'Front Cover' => array(
        			'type' => 'attachment', 
        			),
        		),
```