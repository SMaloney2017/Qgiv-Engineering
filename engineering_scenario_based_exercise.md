# Qgiv Scenario Based Engineering Exercise

## Instructions

Using the below scenario, **architect** a web-based solution “**on paper**” using anything that helps you explain it e.g. pseudo code, UML, plain text, etc.  (no actual code, please).

## Scenario

As an administrator for **Paws for a Cause**, Tonya needs to be able to report on the organization’s users.  Each user has made a transaction to **Paws for a Cause** at some point in time through their web-based donation forms.  

## Tonya specifically needs to:

-	View a list of all users
    - The organization has several thousand
-	Search for users by name, email address, or transaction ID
-	View all of an individual user’s details
-	View a list of transactions for an individual user
    -   Each user generates one or more transactions on an annual basis
    -   All transactions for a user should be visible on their page

## Assumptions & Requirements

-	The solution should be back-end focused
    -   Only document as much of the front-end implementation as is needed to explain the solution
-	Minimal data structures
    -   Each transaction should have the following attributes:
        -   ID
        -   Timestamp
        -   Amount
        -   Status (Accepted, Declined)
        -   Payment Method (Visa, MasterCard, .etc)
-   Each user should have attributes available in the RandomUser.me API:  https://randomuser.me/documentation#results
