fun main() {
    println("Problem #1")
    println("Print Pass if grade is grater or equal to 75")

    var grade1=76
    println("The grade is ")
    println(grade1)
    if(grade1>=75) {
        println("The remarks is Pass")
    }

    println("Problem #2")
    println("Print Pass if grade is grater or equal to 75 otherwise faild")

    var grade2=74
    println("The grade is ")
    println(grade2)
    if(grade2>=75) {
        println("The remarks is Pass")
    }
    else {
        println("Faild")
    }

    println("Problem #3")
    println("Get the sum of fistnumber and second number")

    var num1=5
    var num2=2
    var sum = num1 + num2
    println("sum of num1 and num2")
    println(sum)

    println("Problem #4")
    println("Get the sum of fistnumber and second number")

    var x=1
    while(x<=5){
        println(x)
        x++
    }

    println("Problem #5")
    println("Using for loop print number x until 25")

    var y=1
    for (x in x..25) {
        println(x)
    }
}

main()