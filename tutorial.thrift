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

struct Message {
    1: string text,
    2: i32 time,
    5000:optional Message parent
}

exception InvalidOperation {
    1: i32 what,
    2: string why
}

exception MessageError {
    1: string why,
    2: i32 code
}

service Calculator {
    i32 calculate(1: Work w) throws (1: InvalidOperation ouch)
}

service Messenger {
    Message say(1:required string text, 2: Message msg) throws (1: MessageError ouch)
}
