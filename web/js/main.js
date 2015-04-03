/**
 * @param object Thrift
 */
var App = function (Thrift) {
    // init clients
    this.multiplexer = new Thrift.Multiplexer();
    this.transport = new Thrift.Transport("/api2.php");
};


App.prototype = {
    /**
     * @returns {UserStoreClient}
     */
    getUserStoreClient: function () {
        return this.multiplexer.createClient('UserStore', UserStoreClient, this.transport);
    },

    /**
     * @returns {UserDictionaryStoreClient}
     */
    getUserDictionaryStoreClient: function () {
        return this.multiplexer.createClient('UserDictionaryStore', UserDictionaryStoreClient, this.transport);
    },

    /**
     * @returns {SynchronizerClient}
     */
    getSynchronizerClient: function () {
        return this.multiplexer.createClient('Synchronizer', SynchronizerClient, this.transport);
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

