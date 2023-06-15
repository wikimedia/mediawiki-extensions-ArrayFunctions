-- Variable instantiation
local af = {}
local php

function af.setupInterface()
	-- Interface setup
	af.setupInterface = nil
	php = mw_interface
	mw_interface = nil

	mw = mw or {}
	mw.af = af

	package.loaded['mw.af'] = af
end

-- export
function af.export( value )
	return php.export( value )
end

-- import
function af.import( value )
	if type( value ) ~= 'string' then
		return nil
	end
	return php.import( value )
end

return af
