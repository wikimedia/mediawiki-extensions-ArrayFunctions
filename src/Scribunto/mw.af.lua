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
function af.export( table )
	if not type( slotName ) == 'table' then
		error( 'Invalid parameter type supplied to mw.af.export()' )
	end

	return php.export( table )
end

return af
