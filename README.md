# QuBit Universal Tag Specification Version 0.9 (DRAFT)

Qubit Universal Variables are our suggested way to structure the data presented on your pages. With QuBit Universal Variables, our aim is to help you easily access the pieces of data you need on your pages from your containers.

Below you will see 4 different main objects:
* Page: Details of the page type and category
* User: Details of the logged in user, or the visitor
* Product: Details of the product the user is currently viewing
* Basket: Details of the products the user has added to the 
* Transaction: Details of a purchase or transaction

QuBit OpenTag recommends creating the releavant JavaScript object on your page prior to the OpenTag container script. This will assure the values are present on the page when a script tries to access them.

If a page does not have the variables that are mentioned below, simply do not even declare them. For example, if your pages only have category and no subcategory, just declare your category. Likewise, if you feel the need to extend the variables below or feel like renaming them, please do so; However, please keep in mind the new variable names or the edited ones, because in order to access them from your scripts in your container, you will need to indicate the new variable names.

Below for each object and variable you will see a data type: String or Number; along with examples and comments about what they represent. Please review them carefully.  When you implement them, please make sure that the resultant generated code is be valid JavaScript.

```html
<script>
window.qubitUniversalVariables = {
	/*********************************************************************
	 * Variables that may live on any page which describe the page itself
     *********************************************************************/
	page: {
        // category: String - The type of page this is, i.e: home, product, category, search,
        // basket, checkout, confirmation
		category: "product",

        // subCategory: String A more detailed description of the page, eg: if Category is
        // "category", subCategory may be "Mens Shirts"
		subCategory: "Mens Shirts"
	},

	/*********************************************************************
	 * Variables that may live on any page which describe the current user
	 *********************************************************************/
	user: {
        // name: String - The name of the user
		name: "Name",

        // username: String - The user  name of the logged in user
		username: "username",

        // email: String - The email address of the logged in user
		email: "user@example.com"
		
	},

    /*****************************************************************************
	 * Variables which describe a single product that is being viewed in the page
     *****************************************************************************/

	product: {
		// id: String - The identifier for the item that is being viewed - this is product Id, not SKU id
		id: "ABC123",

		// name: String - The name of the product that is being viewed
		name: "XYZShoes",

		// manufacturer: String - The manufactuter of the product that is being viewed
		manufacturer: "Acme Corp",

		// category: String - The category of the product that is being viewed
		category: "Shoe",

		// subCategory: String - The sub-category of the product that is being viewed
		subCategory: "Trainers",

        // sku: String - The SKU code for the item that is being viewed - this should be unique for items
		// which differ by colour or size. This is for the case where only one SKU are selectable
		sku: "12345678",

        // Array: [String, String, ...] - An SKU code for each item that is being viewed - these should
		// be unique for items which differ by colour or size. This is for the case where multiple SKUs
		// are selectable
		skus: ["1111111", "222222", "33333"],

        // Array: [String, String, ...] - An array of the sizes which are presented for the product being
		// viewed. Each element should be a string, optionally ["small", "medium", "large"] or ["A", "B",
		// "C", "D", "DD"]
		sizes: ["8","9","10"],

		// unitPrice: Number - The cost of a single unit of the item that is being viewed
		unitPrice: 14.99,

		// unitSalePrice: Number: The price of the item taking into account any sales or special
		// circumstances
		unitSalePrice: 10.99,

		// unitPriceCurrency: String: The currency in which the unit price is displayed. Three letter ISO
		// code: GBP, EUR, USD etc.
		unitPriceCurrency: "GBP"
	},

	/**************************************************************************
	 * Variables which describe the current state of the user's shopping basket
	 **************************************************************************/
	basket: {

        // subtotal: Number - A valid number with the total cost of the basket including any known tax per
		// item, but not including shipping or discounts
		subtotal: 12.00,

        // total: Number - A valid number with the total cost of the basket including any known tax,
		// shipping and discounts
		total: 123.00,

        // tax: Number - A valid number with the total amount of potential tax included in the order
		tax: 12.00,

        // shipping: Number - A valid number with the total amount of potential shipping costs included in
		// the order
		shipping: 1.00,

        // currency: String - The standard letter code in capitals for the currency type in which the
		// order is being paid, eg: EUR, USD, GBP
		currency: "GBP",

        // items: Array - An array of item objects
		items: [
            {
                // productId: String - The identifier for the item in the basket - this must be the same
				// for items which differ only by colour or size
    			productId: "ABC123",

                // productSku: String - The SKU code for the item in the basket - this should be unique
				// for items which differ by colour or size
    			productSku: "DEF456",

                // productName: String - The name of the product that is in the basket
    			productName: "White T-Shirt",

                // productManufacturer: String - The manufacturer of the product that is in the basket
    			productManufacturer: "Manufacturer Name",

                // productCategory: String - The category of the product that is in the basket
    			productCategory: "Clothing",

                // productSubCategory: String - The sub-category of the product that is in the basket
    			productSubCategory: "Men's Clothing",

                // productUnitPrice: Number - A number with the cost of a single unit of the item in the
				// basket
    			productUnitPrice: 12.30,

                // quantity: Number - The number of units of this item in the basket
    			quantity: 1,

                // salePrice: Number - The price of the item taking into account any sales due to vouchers
				// or special circumstances
    			salePrice: 10.30
			},

            // In case you have multiple, keep adding more of the same object type to the array
			{
    			productId: "ABC234",
                productSku: "DEF456",
                productName: "Blue T-Shirt",
                productManufacturer: "Manufacturer Name",
                productCategory: "Clothing",
                productSubCategory: "Men's Clothing",
                productUnitPrice: 13.30,
                quantity: 1,
                salePrice: 12.30
			}
	   ]
	},


	/***********************************************
	 * Variables which describe a completed purchase
	 ***********************************************/
	transaction: {
        // orderId: String - A unique identifier for the order
		orderId: "WEB123456",

        // orderSubtotal: Number - A valid number with the total amount the order including tax per item,
		// but not including shipping or discounts
		orderSubtotal: 123.00,

        // orderTotal: Number - A valid number with the total cost including tax, shipping and discounts
		orderTotal: 130.00,

        // orderTax: Number - A valid number with the total amount of tax included in the order
		orderTax: 10.00,

        // orderShipping: Number - A valid number with the total amount of shipping costs included in the
		// order
		orderShipping: 0.00,

        // orderCurrency: String - The standard letter code in captials for the currency type in which
		// the order is being paid, eg EUR, USD, GBP
		orderCurrency: "GBP",

        // city: String - The city to which the order is to be dispatched
		city: "London",

        // state: String - The state to which the order is to be dispatched
		state: "London",

        // country: String - The country to which the order is to be dispatched
		country: "UK",

        // voucher: String - The voucher code entered
		voucher: "MYVOUCHER",

        // voucherDiscount: Number - A valid number with the total amount of discount due to the voucher
		// entered
		voucherDiscount: 0.00

		items: [
			{
                // productId: String - The identifier for the item that has been sold - this must be the
				// same for items which differ only by colour or size.
    			productId: "ABC234"

    			// productSku: String - The SKU code for the item that has been sold. This should be
				// unique for items which differ by colour or size.
                productSku: "DEF456",

    			// productName: String - The name of the product that has been sold.
                productName: "Blue T-Shirt",

    			// productManufacturer: String - The manufacturer of the product that has been sold.
                productManufacturer: "Manufacturer Name",

    			// productCategory: String - The category of the product that has been sold.
                productCategory: "Clothing",

				// productSubCategory: String - The sub-category of the product that is in the basket
    			productSubCategory: "Men's Clothing",

    			// productUnitPrice: Number - A number with the cost of a single unit of the item being
				// sold.
                productUnitPrice: 13.30,

    			// quantity: Number - The number of units being sold.
                quantity: 1,

    			// salePrice: Number - The price of the item taking into account any sales due to
				// vouchers, or special circumstances.
                salePrice: 12.30

                // voucher: Number - The voucher code entered (only necessary if different from
				// transaction)
    			voucher: "MYVOUCHER"
			},
			{
    			productId: "ABC456"
    			productSku: "DEF678",
    			productName: "White T-Shirt",
    			productManufacturer: "Manufacturer Name",
    			productCategory: "Clothing",
    			productUnitPrice: 12.30,
    			quantity: 1,
    			saleAmount: 10.30,
    			voucher: "MYVOUCHER"
			}
		]
	}
}
</script>
```