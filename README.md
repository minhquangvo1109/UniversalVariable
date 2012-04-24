DRAFT
Qubit Universal variables are to be written to a page in the following form. Variables inside <> are to be replaced with the value described within the tokens. The resultant generated code must be valid JavaScript.

```html
<script>
window.qubitUniversalVariables = {
	/****
	* Variables that may live on any page which describe the page itself
	****/
	page: {
		category: "<The type of page this is, eg: home, product, category, search, basket, checkout, confirmation>",
		subCategory: "<A more detailed description of the page, eg: if Category is \"category\", subCategory may be \"Mens Shirts\">"
	},
 
	/**** 
	* Variables that may live on any page which describe the current user
	****/
	user: {
		name: "The name of the user",
		username: "<The user  name of the logged in user>",
		email: "<The email address of the logged in user>"
	},

	/**** 
	* Variables which describe a single product that is being viewed in the page
	****/

	product: {
		// id: String: The identifier for the item that is being viewed - this is product Id, not SKU id.
		id: "ABC123",
		
		// name: String: The name of the product that is being viewed
		name: "XYZShoes",
		
		// manufacturer: String: The manufactuter of the product that is being viewed
		manufacturer: "Acme Corp",
		
		// category: String: The category of the product that is being viewed
		category: "Shoe",
		
		// subCategory: String: The sub-category of the product that is being viewed 
		subCategory: "Trainers",
		
		
		sku: "<The SKU code for the item that is being viewed - this should be unique for items which differ by colour or size. This is for the case where only one SKU are selectable>",
		skus: ["1111111", "222222", "33333"] //<An SKU code for each item that is being viewed - these should be unique for items which differ by colour or size. This is for the case where multiple SKUs are selectable>",
		sizes: ["8","9","10"], //or ["small", "medium", "large"] or ["A", "B", "C", "D", "DD"]  - an array of the sizes which are presented for the product being viewed. Each element should be a string.
		
		// unitPrice: Number: The cost of a single unit of the item that is being viewed
		unitPrice: 14.99,
		
		// unitSalePrice: Number: The price of the item taking into account any sales or special circumstances
		unitSalePrice: 10.99,
		
		// unitPriceCurrency: String: The currency in which the unit price is displayed. Three letter ISO code: GBP, EUR, USD etc.
		unitPriceCurrency: "GBP"
	},

	/**** 
	* Variables which describe the current state of the user�s shopping basket
	****/
	basket: {
		subtotal: <A valid number with the total cost of the basket including any known tax per item, but not including shipping or discounts>,
		total: <a valid number with the total cost of the basket including any known tax, shipping and discounts>,
		tax: <a valid number with the total amount of potential tax included in the order>,
		shipping: <a valid number with the total amount of potential shipping costs included in the order>,
		currency: "<The standard letter code in capitals for the currency type in which the order is being paid, eg: EUR, USD, GBP>",
		items: [
			{
			productId: "<The identifier for the item in the basket - this must be the same for items which differ only by colour or size>"
			productSku: "<The SKU code for the item in the basket - this should be unique for items which differ by colour or size>",
			productName: "<The name of the product that is in the basket>",
			productManufacturer: "<The manufacturer of the product that is in the basket>",
			productCategory: "<The category of the product that is in the basket>",
			productSubCategory: "<The sub-category of the product that is in the basket>",
			productUnitPrice: <A number with the cost of a single unit of the item in the basket>,
			quantity: <The number of units of this item in the basket>,
			salePrice: <The price of the item taking into account any sales due to vouchers, or special circumstances>
			},
			{ // In case you have multiple, keep adding more of the same object type to the array
			productId: "<The identifier for the item in the basket - this must be the same for items which differ only by colour or size>"
			productSku: "<The SKU code for the item in the basket - this should be unique for items which differ by colour or size>",
			productName: "<The name of the product that is in the basket>",
			productManufacturer: "<The manufacturer of the product that is in the basket>",
			productCategory: "<The category of the product that is in the basket>",
			productSubCategory: "<The sub-category of the product that is in the basket>",
			productUnitPrice: <A number with the cost of a single unit of the item in the basket>,
			quantity: <The number of units of this item in the basket>,
			salePrice: <The price of the item taking into account any sales due to vouchers, or special circumstances>
			}
	 ]
	},

	/*** 
	* Variables which describe a completed purchase
	***/
	transaction: {
		orderId: "<a unique identifier for the order>",
		orderSubtotal: <a valid number with the total amount the order including tax per item, but not including shipping or discounts>,
		orderTotal: <a valid number with the total cost including tax, shipping and discounts>,
		orderTax: <a valid number with the total amount of tax included in the order>,
		orderShipping: <a valid number with the total amount of shipping costs included in the order>,
		orderCurrency: "<The standard letter code in captials for the currency type in which the order is being paid, eg EUR, USD, GBP>",
		city: "<the city to which the order is to be dispatched>",
		state: "<the state to which the order is to be dispatched>",
		country: "<the country to which the order is to be dispatched>",
		voucher: "<The voucher code entered>",
		voucherDiscount: <a valid number with the total amount of discount due to the voucher entered>
		items: [
			{
			productId: "<The identifier for the item that has been sold - this must be the same for items which differ only by colour or size>"
			productSku: "<The SKU code for the item that has been sold - this should be unique for items which differ by colour or size>",
			productName: "<The name of the product that has been sold>",
			productManufacturer: "<The manufacturer of the product that has been sold>",
			productCategory: "<The category of the product that has been sold>",
			productUnitPrice: <A number with the cost of a single unit of the item being sold>,
			quantity: <The number of units being sold>,
			saleAmount: "<The price of the item taking into account any sales due to vouchers, or special circumstances>",
			voucher: "<The voucher code entered (only necessary if different from transaction)>"
			},
			{
			productId: "<The identifier for the item that has been sold - this must be the same for items which differ only by colour or size>"
			productSku: "<The SKU code for the item that has been sold - this should be unique for items which differ by colour or size>",
			productName: "<The name of the product that has been sold>",    
			productManufacturer: "<The manufacturer of the product that has been sold>",
			productCategory: "<The category of the product that has been sold>",
			productUnitPrice: <A number with the cost of a single unit of the item being sold>,
			quantity: <The number of units being sold>,
			saleAmount: "<The price of the item taking into account any sales due to vouchers, or special circumstances>",
			voucher: "<The voucher code entered (only necessary if different from transaction)>"
			}
			]
	}
}
</script>
```