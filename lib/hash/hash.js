var hashMe = function (file, callbackFunction) {
    
    var thisObj = this,
        _binStart = "",
        _binEnd = "",
        callback = "",
        fileManager1 = new FileReader,
        fileManager2 = new FileReader;
        
    this.setBinAndHash = function (startOrEnd, binData) {
        
        switch (startOrEnd) {
            case 0:
                this._binStart = binData;
                break;
            case 1:
                this._binEnd = binData
        }
        
        this._binStart && this._binEnd && this.md5sum(this._binStart, this._binEnd)
    };
    
    this.md5sum = function (start, end) {
        this._hash = rstr2hex(rstr_md5(start + end));
        callback(this._hash);
    };
    
    this.getHash = function() {
        return this._hash;
    };
    
    this.calculateHashOfFile = function (file) {

//        var start = file.slice(0, 65536);
//        var end = file.slice(file.size - 65536, file.size);
        blobSlice = File.prototype.mozSlice || File.prototype.webkitSlice || File.prototype.slice;
        var start = blobSlice.call(file, 0, 65536);
        var end = blobSlice.call(file, file.size - 65536, file.size);

        //extend FileReader
        if (!FileReader.prototype.readAsBinaryString) {
            FileReader.prototype.readAsBinaryString = function (fileData) {
                var binary = "";
                var pt = this;
                var reader = new FileReader();
                reader.onload = function (e) {
                    var bytes = new Uint8Array(reader.result);
                    var length = bytes.byteLength;
                    for (var i = 0; i < length; i++) {
                        binary += String.fromCharCode(bytes[i]);
                    }
                    pt.content = binary;
                    $(pt).trigger('onload');
                }
                reader.readAsArrayBuffer(fileData);
            }
        }
        //
        fileManager1.onload = function (f) {
            if (fileManager1.result) fileManager1.content = fileManager1.result;
            var base64Data = btoa(fileManager1.content);
            thisObj.setBinAndHash(0, base64Data);
        };

        fileManager2.onload = function (f) {
            if (fileManager2.result) fileManager2.content = fileManager2.result;
            var base64Data = btoa(fileManager2.content);
            thisObj.setBinAndHash(1, base64Data);
        };

		fileManager1.readAsBinaryString(start);
        fileManager2.readAsBinaryString(end);
    };
    
    this.calculateHashOfFile(file);
    callback = callbackFunction;

};