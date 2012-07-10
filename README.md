# QuBit Universal Variable Specification Version 1.0.0

QuBit Universal Variable is our suggested way to structure the data presented on your pages. With QuBit Universal Variable, our aim is to help you easily access the pieces of data you need on your pages from your containers.

Below you will see 7 different main objects:
* [User](#user): Details of the logged in user, or the visitor
* [Page](#page): Details of the page type and category
* [Product](#product): Details of the product that is being viewed or present in basket, checkout, recommendation, etc.
* [Basket](#basket): Details of the user basket/shopping cart
* [Transaction](#transaction): Details of a purchase or transaction
* [Product Listing](#listing): Details of a category or search result page
* [Recommendation](#recommendation): Details of a recommendation

QuBit OpenTag recommends creating the releavant JavaScript object on your page prior to the OpenTag container script. This will assure the values are present on the page when a script tries to access them.

If a page does not have the variables of an object that are mentioned below, simply do not even declare them. For example, if your pages only have category and no subcategory, just declare your category. Likewise, if you feel the need to extend the objects below or feel like renaming them, please do so. However, please take a note of the new variable names or the edited ones, because in order to access them from your scripts in your OpenTag container, you will need to use the new variable names.

Below for each object and variable you will see a data type: String, Number, Boolean, Array; along with examples and comments about what they represent. Please review them carefully.  Additionally, when you implement them, please make sure that the resultant generated code is a valid JavaScript.

## Namespace

All universal variable should be assigned to `window` object under `universal_variable` object.

example:

``` javascript
window.universal_variable = {};
```


## Version

A version variable defines the current specificaiton version is used.

```javascript
window.universal_variable.version = "1.0.0";
```

## Page

The Page Universal Variable describes a page type with category or sub-category. It may be created on any page. 

Specification:

``` javascript
window.universal_variable = {
	page: {
		// {String}
		// The type of page this is, i.e: home, product, category, search,
		// basket, checkout, confirmation
		category: "product",

		// {String}
		// A more detailed description of the page, eg: if Category is
		// "category", subCategory may be "Mens Shirts"
		subcategory: "Mens Shirts",

		// {String}
		// Optional. The system environment through which this page
		// is being viewed. e.g. production, uat, development
		environment: "production",

		// {String}
		// Optional. Specify a page variation if the current page
		// represents a different test variation.
		variation: "Original",

		// {String}
		// Optional. A unique identifier to signify the rivision of the current page.
		revision: "1.1"
	}
}
```

or

``` javascript
window.universal_variable.page = {
	// {String}
	// The type of page this is, i.e: home, product, category, search,
	// basket, checkout, confirmation
	category: "product",

	// {String}
	// A more detailed description of the page, eg: if Category is
	// "category", subCategory may be "Mens Shirts"
	subcategory: "Mens Shirts"
}
```

## User

The User Universal Variable describes a user. It may be created on any page.

Specification:
``` javascript
window.universal_variable = {
	user: {
		// {String}
		// The full name of the user.
		name: "Name",

		// {String}
		// The name with which the user uses to login
		username: "username",

		// {String}
		// An internal user identifier
		user_id: "",

		// {String} 
		// The email address of the logged in user
		email: "user@example.com",

		// {String}
		// User preferred Language from a language standard list
		// ISO language code list ISO 639-1
		language: "en-gb",

		// {Boolean}
		// true if the user is a returning user, otherwise false
		returning: true,
		
		// {Number}
		// Facebook id of the logged in user
		facebook_id: 12345678901232345,

		// {String}
		// Twitter id
		twitter_id: "myid"

	}
}
```

## Product

The Product universal variable describes a single product information. `Product` is usually displayed in a number of pages, or even sections within a page. The Product Universal Variable provides a single specification to desribe a product information. 

When the variable is used with `product` key, it represents a single product page. It can also composite with other universal variables:
* [`product` variable](#product) itself may also have an array of products as linked products
* [`listing` variable](#listing) may have an array of products as search results
* [`recommendation` variable](#recommendation) may have an array of products as recommended items for purchase

`LineItem` universal variable also uses product object to describe a product added to basket or a purchased product. See [LineItem](#lineitem) section for further detailed description.

The product displayed on the page can be represented in the following specification:

```javascript
window.universal_variable.product = Product
```

The `Product` variable should follow the specificatio below.

Product Specification:

```javascript
{

	// {String}
	// The product identifier or ID. This is NOT SKU id
	id: "ABC123",

	// {String}
	// The SKU code for the product is being viewed
	sku_code: 123,

	// {String}
	// URL for the the product
	url: "http://wwww.example.com/product?=ABC123", 

	// {String}
	// The name of the product
	name: "XYZShoes",

	// {String}
	// Long product description
	description: "most popular shoes in our shop",

	// {String}
	// The manufactuter of the product that is being viewed
	manufacturer: "Acme Corp",

	// {String}
	// The category of the product that is being viewed
	category: "Shoe",

	// {String}
	// The sub-category of the product that is being viewed
	subcategory: "Trainers",

	// [Array] of {Product} Universal Variable
	// An array of sub products
	linked_products: [Product, Product, Product, ...],

	// {String}
	// A text describes color of the item being viewed
	color: "WHITE",
	
	// {String}
	// The size user currently selected
	size: "M",

	// {Number} 
	// A number indicates the stock avalability. Set the value to 0 if the item is out of stock
	stock: 10,

	// {Number}
	// The cost of a single unit of the item that is being viewed
	unit_price: 123.00,

	// {Number}
	// The price of the item taking into account any sales or special
	// circumstances
	unit_sale_price: 100.00,

	// {String}
	// The standard letter code in captials for the currency type.
	// The currency for this product, eg EUR, USD, GBP
	currency: "GBP",

	// {Number}
	// The voucher code entered (only necessary if different from transaction)
	voucher: "MYVOUCHER"
}
```


## LineItem
The LineItem universal variable describes a number of [Product](#product) that has been added to the basket or processed in a transaction.

Specification:
```javascript
{
	// {Product}
	// The Product Universal Variable
	product: Product,

	// {Number}
	// The number of product a user has added to the basket or purchased in a transaction
	quantity: 1,

	// {Number}
	// The total cost of this line item including tax, excluding shipping
	subtotal: 100.00
}
```


## Basket

The Basket Universal Variable describes the current state of the a user's shopping basket. It also requires a list of [Product Universal Variable](#product) in the `item` array.

Specification:
``` javascript
window.universal_variable = {
	basket: {

		// {String}
		// The basket ID or cart ID
		id: "BASKET2203",

		// {String}
		// The standard letter code in capitals for the currency type in which the
		// order is being paid, eg: EUR, USD, GBP
		currency: "GBP",

		// {Price}
		// A valid number with the total cost of the basket, but not including shipping or discounts
		subtotal: 123.00,

		// {Boolean}
		// A boolean true or false to indicate whether subtotal includes tax
		subtotal_include_tax: true,

		// {Number}
		// A valid number with the total amount of potential tax included in the order
		tax: 12.00,

		// {Number}
		// A valid number with the total amount of potential shipping costs included in
		// the order
		shipping_cost: 1.00,

		// {String}
		// Optional. Describes the shipping method
		shipping_method: "Standard Mail",

		// {Number}
		// A valid number with the total cost of the basket including any known tax,
		// shipping and discounts
		total: 123.00,

		// [Array] of {LineItem} Universal Variable
		// An array of LineItem
		line_items: [LineItem, LineItem, LineItem, ...]
	}
}
```


## Transaction
Transaction Universal Variable describes a completed purchase. It also requires a list of [Product Universal Variable](#product) in the `item` array.

Specification:
```javascript
window.universal_variable = {
	transaction: {
		// {String}
		// A unique identifier for the order
		order_id: "WEB123456",

		// {String}
		// The standard letter code in captials for the currency type in which
		// the order is being paid, eg EUR, USD, GBP
		currency: "GBP",

		// {Number}
		// A valid number with the total amount the order,
		// but not including shipping or discounts
		subtotal: 123.00,

		// {Boolean}
		// A boolean true or false to indicate whether subtotal includes tax
		subtotal_include_tax: true,


		// {String}
		// The voucher code entered
		voucher: "MYVOUCHER",

		// {Number}
		// A valid number with the total amount of discount due to the voucher entered
		voucher_discount: 0.00,

		// {Number}
		// A valid number with the total amount of tax included in the order
		tax: 10.00,

		// {Number}
		// A valid number with the total amount of shipping costs included in the
		// order
		shipping_cost: 1.00,

		// {String}
		// Optional. Describes the shipping method
		shipping_method: "Standard Mail",

		// {Number}
		// A valid number with the total cost including tax, shipping and discounts
		total: 130.00,

		delivery: {

			// {String}
			// The full name of the delivery receiver.
			name: "Full Name",

			// {String}
			// Optional. The full addresss including the street number, but without the city.
			address: "234 High Street",

			// {String}
			// The city to which the order is to be dispatched
			city: "London",

			// {String}
			// The state to which the order is to be dispatched
			state: "London",

			// {String}
			// The post code to which the order is to be dispatched
			postcode: "SW1 1AB",
			
			// {String}
			// The country to which the order is to be dispatched
			country: "UK",
		},


		billing: {
		
			// {String}
			// The full name of the delivery receiver.
			name: "Full Name",
			
			// {String}
			// Optional. The full addresss including the street number, but without the city.
			address: "234 High Street",

			// {String}
			// The city to which the billing is set
			city: "London",

			// {String}
			// The state to which the billing is set
			state: "London",

			// {String}
			// The postcode to which the billing is set
			postcode: "SW1 1AB",
			
			// {String}
			// The country to which the billing is set
			country: "UK",
		},

		// [Array] of {LineItem} Universal Variable
		// An array of LineItem
		line_items: [LineItem, LineItem, LineItem, ...]
	}
}
```

## Listing

The listing Universal Variable describes a product list, for example a category page or search results page. It contains a list of [Product Universal Variable](#product)

Specification:
```javascript
window.universal_variable = {
	listing: {
		// {String}
		// Describes the search query by keywords or text
		query: "shoes on sale",

		// An [Array] of {Product}
		// A list of Product universal variables, describes the search result
		items: [Product, Product, Product, ...]
	}
}
```

## Recommendation

Recommendation Universal Variable describes a recommended product list. It contains a list of [Product Universal Variable](#product)

Specification:
```javascript
window.universal_variable = {
	recommendation: {
		// An [Array] of {Product}
		// A list of Product universal variables, describes the recommendation result
		items: [Product, Product, Product, ...]
	}
}
```
