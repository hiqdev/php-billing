@startuml

!define Entity(name,desc) class name as "desc" << (E,#FFAAAA) >>
!define Outer(name,desc) class name as "desc" << (O,#DDDDDD) >>
!define ValueObject(name,desc) class name as "desc" << (V,#AAFFAA) >>
' we use bold for primary key
' green color for unique
' and underscore for not_null
!define primary_key(x) x INTEGER
!define foreign_key(x, y) <i>x</i> --> <b>y</b>
!define unique(x) <color:green>x</color>
!define not_null(x) <u>x</u>
!define value_object(x, y) x <b>y</b>
' other tags available:
' <i></i>
' <back:COLOR></color>, where color is a color name or html color code
' (#FFAACC)
' see: http://plantuml.com/classes.html#More
hide methods
hide stereotypes

package "Tariff plans" {
    Entity(plan, "Plan") {
        primary_key(id)
        value_object(type, Type)
    }

    Entity(price, "Price") {
        primary_key(id)
        foreign_key(plan, Plan)
        foreign_key(target, Target)
        value_object(price, Money)
        value_object(prepaid, Quantity)
    }
}

package "Payments" {
    Entity(bill, "Bill") {
        primary_key(id)
        foreign_key(customer, Customer)
        foreign_key(target, Target)
        foreign_key(plan, Plan)
        value_object(type, Type)
        value_object(sum, Money)
        value_object(quantity, Quantity)
        comment TEXT
        time DATETIME
    }

    Entity(charge, "Charge") {
        primary_key(id)
        foreign_key(bill, Bill)
        foreign_key(target, Target)
        foreign_key(action, Action)
        value_object(type, Type)
        value_object(usage, Quantity)
        value_object(sum, Money)
    }
}

package "Subscriptions" {
    Entity(sale, "Sale") {
        primary_key(id)
        foreign_key(target, Target)
        foreign_key(customer, Customer)
        foreign_key(plan, Plan)
        time DATETIME
    }

    Outer(customer, "Customer") {
        primary_key(id)
        foreign_key(seller, Customer)
    }

    Outer(target, "Target") {
        primary_key(id)
        value_object(type, Type)
        name TEXT
    }
}

package "Metered activity" {
    Outer(order, "Order") {
        primary_key(id)
    }

    Entity(action, "Action") {
        primary_key(id)
        foreign_key(parent, Action)
        foreign_key(customer, Customer)
        foreign_key(order, Order)
        foreign_key(target, Target)
        value_object(type, Type)
        value_object(quantity, Quantity)
        time DATETIME
    }
}

package "Value Objects" {
    ValueObject(type, "Type") {
        foreign_key(parent, Type)
        name TEXT
    }

    ValueObject(money, "Money") {
        sum INTEGER [in cents]
        value_object(currency, Type)
    }

    ValueObject(quantity, "Quantity") {
        quantity NUMERIC
        value_object(unit, Type)
    }
}

type --> type : Parent

price --> plan
price -down-> target

sale --> customer
sale --> plan
sale -down-> target

customer --> customer : Seller

action --> order
action --> action : Parent
action -down-> target
action --> customer

charge -left-> bill
charge -up-> action
charge -up-> target

bill -up-> customer
bill -up-> plan
bill -up-> target

charge -[hidden]-> money

@enduml
