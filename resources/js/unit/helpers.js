
export default {
    getCache(key) {
        var val = localStorage.getItem(key);//获取存储的元素
        console.log(val);

        if (!this.storageAvailable('localStorage')) {
            return null;
        }

        console.log(val != null);
        if (val != null) {
            var parse = JSON.parse(val);
            var data = typeof parse == "object" ? parse : val;//解析出json对象

            if (data.expired_time <= new Date().getTime()) {
                localStorage.removeItem(key)
                return null;
            }

            return data;
        }
        return null;
    },
    setCache(key, value, seconds) {
        if (!this.storageAvailable('localStorage')) {
            return null;
        }
        
        var expired = new Date().getTime() + seconds * 1000;
        localStorage.setItem(key, JSON.stringify({ "val": value, "expired_time": expired }));
    },
    storageAvailable(type) {
        try {
            var storage = window[type],
                x = '__storage_test__';
            storage.setItem(x, x);
            storage.removeItem(x);
            return true;
        } catch(e) {
            return e instanceof DOMException && (
                // everything except Firefox
                e.code === 22 ||
                // Firefox
                e.code === 1014 ||
                // test name field too, because code might not be present
                // everything except Firefox
                e.name === 'QuotaExceededError' ||
                // Firefox
                e.name === 'NS_ERROR_DOM_QUOTA_REACHED') &&
                // acknowledge QuotaExceededError only if there's something already stored
                storage.length !== 0;
        }
    }
}