namespace php Hellowords

typedef i64 GUID
typedef i64 USN
typedef string AuthToken
typedef i32 Timestamp

enum Language {
    RU,
    EN
}

struct UserInfo {
    1:GUID id,
    2:string username
}

struct AuthResult {
    2:UserInfo userInfo,
    3:AuthToken authToken
}

struct Expression {
    1:GUID id,
    2:string chars,
    3:Language lang
}

struct Syntrans {
    1:GUID id,
    2:Expression word,
    3:Expression trans,
    4:USN updateSequenceNum,
    5:Timestamp createdAt,
    6:optional Timestamp deletedAt
}

struct SyncState {
    1:Timestamp time,
    2:Timestamp fullSyncBefore,
    3:USN updateCount
}

struct SyncChunk {
    1:Timestamp time,
    2:USN chunkHighUSN,
    3:USN updateCount,
    4:list<Syntrans> syntransList
}

exception AccessViolationException {
    1:string message
}

exception InvalidRequestException {
    1:string message
}

exception NotFoundException {
    1:string message
}

service UserStore {
    AuthResult getSession(1:AuthToken authToken) throws (1:AccessViolationException ave),

    AuthResult authenticate(1:string username, 2:string password)
        throws (1:AccessViolationException ave, 2:InvalidRequestException ire)
}

service UserDictionaryStore {
    Syntrans createSyntrans(1:AuthToken authToken, 2:Syntrans syntrans)
        throws (1:AccessViolationException ave, 2:InvalidRequestException ire),

    USN deleteSyntrans(1:AuthToken authToken, 2:GUID guid)
        throws (1:AccessViolationException ave, 2:NotFoundException nfe)
}

service Synchronizer {
    SyncState getSyncState(1:AuthToken authToken) throws (1:AccessViolationException ave),

    SyncChunk getSyncChunk(1:AuthToken authToken, 2:USN afterUSN = 0, 3:i32 maxEntities = 100)
        throws (1:AccessViolationException ave)
}
