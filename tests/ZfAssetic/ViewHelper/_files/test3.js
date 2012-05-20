var test = function(name) {
	var compress = this;
	if (compress) {
		compress.name = name;
	}

	return compress;
};

test('Sylvain Filteau');
