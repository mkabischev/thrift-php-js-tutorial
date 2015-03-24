/**
 * @param object Thrift
 * @param object localStorage
 * @returns {App.mainAnonym$0}
 */
var App = function (Thrift) {

    // init clients

    var multiplexer = new Thrift.Multiplexer();
    var transport = new Thrift.Transport("/api2.php");

    return {
        /**
         * @returns {UserStoreClient}
         */
        getUserStoreClient: function () {
            return multiplexer.createClient('UserStore', UserStoreClient, transport);
        },
        /**
         * @returns {UserDictionaryStoreClient}
         */
        getUserDictionaryStoreClient: function () {
            return multiplexer.createClient('UserDictionaryStore', UserDictionaryStoreClient, transport);
        },
        /**
         * @returns {SynchronizerClient}
         */
        getSynchronizerClient: function () {
            return multiplexer.createClient('Synchronizer', SynchronizerClient, transport);
        },
        /**
         * @returns {Array}
         */
        getLanguageMap: function () {
            var map = [];
            for (var id in Language) {
                map[Language[id]] = id;
            }
            return map;
        }
    };
};
