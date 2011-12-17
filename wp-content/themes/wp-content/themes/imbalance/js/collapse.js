		animatedcollapse.addDiv('categories', 'fade=0,speed=400,group=srv,hide=1')
		animatedcollapse.addDiv('archives', 'fade=0,speed=400,group=srv,hide=1')
		animatedcollapse.addDiv('search', 'fade=0,speed=400,group=srv,hide=1')

		animatedcollapse.ontoggle=function($, divobj, state){ //fires each time a DIV is expanded/contracted
			//$: Access to jQuery
			//divobj: DOM reference to DIV being expanded/ collapsed. Use "divobj.id" to get its ID
			//state: "block" or "none", depending on state
		}

		animatedcollapse.init()