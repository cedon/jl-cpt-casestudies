# JL CPT Fitness Case Study

[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg?style=plastic)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html)

JL CPT Fitness Case Study is a WordPress plugin the creates a Case Study custom post type to help Fitness professionals.

This plugin is is based on the custom post type helper class described by Gils Jorissen in the [Custom Post Type Helper Class](https://code.tutsplus.com/articles/custom-post-type-helper-class--wp-25104) and then modified to accept a different configuration of parameters to define meta box fields.

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

Note: The class will first check to see if the taxonomy is already registered. In the event that it is, then it will simply associate the taxonomy with the custom post type.