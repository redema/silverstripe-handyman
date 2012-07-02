# SilverStripe handyman Module

Handyman is a collection of useful Extensions and Decorators for SilverStripe
and Sapphire which helps developers to perform common tasks and workarounds.

## Maintainer Contact

Redema AB <http://redema.se/>

Author: Erik Edlund <erik.edlund@redema.se>

## Requirements

*SilverStripe 3.0 is not yet supported.*

 * PHP: 5.2.4+ minimum.
 * SilverStripe: 2.4.4 minimum (previous versions has never been tested).

## Installation Instructions

 * Place this directory in the root of your SilverStripe installation. Make sure
   that the folder is named `handyman` if you are planning to run the unit
   tests.

 * Visit http://www.yoursite.example.com/dev/build?flush=all to rebuild the
   manifest.

## Usage Overview

### Enforcing values for db fields of DataObjects

`DataObjectEnforceDBValueDecorator` makes it possible to enforce values for
DataObject db fields. How it works is most easily demonstrated by an example:

        class Example extends DataObject {
            public static $db = array(
                'Field1' => 'Text',
                'Field2' => 'Int'
            );
            public static $enforce_db_value = array(
                'Field1' => 'StaticValue',
                'Field2' => '->getField2DynamicValue'
            );
            public function getField2DynamicValue() {
                return 1 + 1;
            }
        }

`Field1` and `Field2` of `Example` will always be assigned the values
determined by the `$enforce_db_value` array on the onBeforeWrite-event for
`Example` objects.

All fields with enforced values are transformed to readonly in getCMSFields()
and getFrontEndFields().

### Automatically handle publishing and unpublishing of Versioned DataObjects
### through a has_one relation

`DataObjectOnVersioningDecorator` makes it easier to handle versioned
DataObjects through a has_one relation. It will make sure that the versioned
DataObjects are published and unpublished automatically when their referenced
DataObject is.

        class Page extends SiteTree {
            public static $has_many = array(
                'Quotes' => 'Quote'
            );
        }
        class Quote extends DataObject {
            public static $has_one = array(
                'Page' => 'Page'
            );
            public static $has_one_on_versioning = array(
                'Page' => true
            );
            public static $extensions = array(
                "Versioned('Stage', 'Live')"
            );
        }

In the above example, all Quotes for a Page will be published and unpublished
when the page is.

`DataObjectOnVersioningDecorator` can only support the stages `Stage` and `Live`
due to limitations in SilverStripes `Versioned` decorator.

If you need to use `DataObjectOnVersioningDecorator` with non-SiteTree
DataObjects as the base object you need to add `DataObjectOnVersioningDecorator`
and `DataObjectVersionedMethodDecorator` to the DataObject in question and then
use the doPublish() and doUnpublish() methods to handle publishing and
unpublishing.

        class Product extends DataObject {
            public static $has_many = array(
                'Attributes' => 'ProductAttribute'
            );
            public static $extensions = array(
                'DataObjectOnVersioningDecorator',
                'DataObjectVersionedMethodDecorator',
                "Versioned('Stage', 'Live')"
            );
        }
        class ProductAttribute extends DataObject {
            public static $has_one = array(
                'Product' => 'Product'
            );
            public static $has_one_on_versioning = array(
                'Product' => true
            );
            public static $extensions = array(
                "Versioned('Stage', 'Live')"
            );
        }

It is also possible to use the above method to cascade versioning through
many layers of `has_many` - `has_one` relations, but deep nesting will
doubtlessly hurt performance.

