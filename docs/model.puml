@startuml

legend right
        |<#FFDDDD>  <b>E</b>  |  <b>Entity</b> |
        |<#DDFFDD>  <b>V</b>  |  <b>Value Object</b>  |
        |<#FFFFDD>  <b>A</b>  |  <b>Auxiliary</b> |
endlegend

!define Entity(name,desc) class name as "desc" << (E,#EEEEEE) >> #FFDDDD
!define ValueObject(name,desc) class name as "desc" << (V,#FFFFFF) >> #DDFFDD
!define Auxiliary(name,desc) class name as "desc" << (A,#FFFFFF) >> #FFFFDD
!define primary_key(x) x INTEGER
!define foreign_key(x, y) <i>x</i> --> <b>y</b>
!define value_object(x, y) x <b>y</b>
hide methods
hide stereotypes

package "Tariff plans" {
    Entity(plan, "Plan") {
        primary_key(id)
    }

    Entity(price, "Price") {
        primary_key(id)
        foreign_key(plan, Plan)
        foreign_key(target, Target)
        value_object(type, Type)
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
        foreign_key(parent, Charge)
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

    Auxiliary(customer, "Customer") {
        primary_key(id)
        foreign_key(seller, Customer)
    }

    Auxiliary(target, "Target") {
        primary_key(id)
        value_object(type, Type)
        name TEXT
    }
}

package "Metered activity" {
    Auxiliary(order, "Order") {
    }

    Entity(action, "Action") {
        primary_key(id)
        foreign_key(parent, Action)
        foreign_key(customer, Customer)
        foreign_key(target, Target)
        value_object(type, Type)
        value_object(quantity, Quantity)
        time DATETIME
    }
}

package "Value Objects" {
    ValueObject(type, "Type") {
        value_object(parent, Type)
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

type -up-> type : Parent

price --> plan
price -down-> target

sale --> customer
sale --> plan
sale -down-> target

customer -up-> customer : Seller

action -up-> action : Parent
action -down-> target
action --> customer

order "1" -up-{ "N" action

charge -up-> charge : Parent
charge -left-> bill
charge -up-> action
charge -up-> target

bill -up-> customer
bill -up-> plan
bill -up-> target

charge -[hidden]-> money

@enduml
