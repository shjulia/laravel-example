export const StringToDateMixin = {
    methods: {
        stringToDate(array, subCount, dateIndex) {
            var parsedFirst = [];
            parsedFirst.push(array[0]);
            for (var i = 1; i < array.length; i++) {
                parsedFirst[i] = [];
                for (var j = 0; j < subCount; j++) {
                    if (dateIndex == j) {
                        parsedFirst[i][j] = new Date(array[i][j]);
                    } else {
                        parsedFirst[i][j] = array[i][j];
                    }
                }
            }
            return parsedFirst;
        }
    }
};