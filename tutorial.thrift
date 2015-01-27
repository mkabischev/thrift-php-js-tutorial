namespace php tutorial

enum Operation {
    ADD = 1,
    SUBTRACT = 2,
    MULTIPLY = 3,
    DIVIDE = 4,
    POW = 5
}

struct Work {
    1: i32 num1 = 0,
    2: i32 num2,
    3: Operation op
}

exception InvalidOperation {
    1: i32 what,
    2: string why
}

service Calculator {
    i32 calculate(1:Work w) throws (1:InvalidOperation ouch)
}

