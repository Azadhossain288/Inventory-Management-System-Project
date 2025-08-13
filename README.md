Project:Inventory Management System

An Inventory Management System is a database-driven application that allows authorized users(Here,users means admin or Manager) to control and track all inventory-related activities, including the creation, updating, and management of products, suppliers, and purchase records.

In this system:

1.Users are responsible for every data entry and update, ensuring accountability through Created_by and Updated_by fields.
2.Products store detailed item information such as name, expiry date, price, and description.
3.Suppliers contain all supplier contact and address details.
4.Purchases record product acquisition from suppliers, including quantities, costs, and remarks.
5.The relationships between entities ensure that all operations are traceable, and every product and purchase can be linked back to the responsible user.

Core Modules:

User Management:
Roles: Admin, Manager, Staff

Functionalities:
1.Login/Logout system (secure authentication)
2.Role-based access control, with Admin having full control over the entire system
3.User creation, editing, and deletion (Admin only)
4.Ability to manage all core modules — Products, Suppliers, and Purchases — according to role permissions
5.Complete tracking of actions through Created_by and Updated_by fields for accountability

Product Management
Funtionalities:
1. Fields: Product ID, Name, Category, Quantity, Unit Price, Expiry Date, Description
2. Functionalities:
3.Add/Edit/Delete/View products
4.Search/filter products by name/category

Supplier Management
Functionalities:
1.Add new supplier details (name, contact, address, email)
2.Edit/update supplier information
3.Delete supplier records (if no dependent purchase history, or with proper archival)
4.View/search/filter suppliers by name, location, or contact
5.Link suppliers to products through the Product_Supplier table
6.Record purchases from suppliers in the Supplier_Purchases table
7.Track Created_by and Updated_by fields for accountability


Supplier Purchase Management
Functionalities:
1.Record new purchase transactions from suppliers
2.Select supplier and related products for each purchase
3.Enter purchase details: quantity, price, total cost, purchase date, and remarks
4.Edit/update existing purchase records
5.Delete purchase records (with proper role permissions)
6.View/search/filter purchases by supplier, product, or date range
7.Link purchase records to both Suppliers and Products via foreign keys
8.Track Created_by and Updated_by fields to know which user recorded or modified each purchase



Technologies Used:
● Frontend: HTML, CSS, JavaScript
● Backend: PHP
● Database: MySQL


We are 4 team members with this project works:
1.Azad Hossain(231_115_039)->Frontened+Database+Supplier Management(Whole Database Design)
2.Abdul Mumin Tuhal(231_115_030)->User Management
3.Amad Ahmed(231_115_023)->Product Management
4.Jamil Ahmed Srijon(231_115_031)->Supplier Purchase Management




Youtube link: https://youtu.be/X-oaT5TEcgU?si=VHI0UkAF_En4ghWP






