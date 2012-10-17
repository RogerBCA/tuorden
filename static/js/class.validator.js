var iRules = {
    messages : false,
    core:{
        title:'App Messages'        
    },
	doc:{
		regex: /^.{8,32}$/,
		msg: 'Invalid ID number.'
	}, 
	name:{
		regex: /^.{3,128}$/,
		msg: 'Puede contener sólo números, letras y espacios.'
	}, 
	mail:{
		regex: /^([\w\d_\.\-])+\@(([\w\dáéíóúñ\-])+\.)+([\w\d]{2,4})+$/,
		msg: 'Not a valid email.<br/>Example: usuario@dominio.com'
	},
	password:{
		regex: /^[\w\d]{3,18}$/,
		msg: 'Only contains number and words.'
	}, 
	telephone:{
		regex: /^[\d()+\- ]{7,24}$/,
		msg: 'Example: 425-2354 , 051(511) +01 254 25402'
	},
	number:{
		regex: /^\d+$/,
		msg: 'Numbers Only.'
	},
	character:{
		regex: /^[\w\d]$/,
		msg: 'Accept Only Alpha-Numeric Characters.'
	},
	content:{
		regex: /^(\s|.){2,}$/,
		msg: 'Campo Obligatorio.'
	},
	date:{
		regex:  /^\d{4}-\d{2}-\d{2}$/,
		msg: 'Ingrese una fecha'
	},
	select:{
		regex: /^.{1,128}$/,
		msg: 'Choose one'
	},
	bool:{
		regex: /^0|1$/,
		msg: 'Select.'
	},
	image:{
		regex: /\.(jpg|jpeg|gif|png)$/i,
		msg: 'Image type Invalid.'
	},
	url:{
		regex:  /^((ht|f)tp(s?):\/\/)?(([0-9a-z_!~*\'\(\)\.\&=\+\$%\-]+: )?[0-9a-z_!~*'().&=+$%-]+@)?(([0-9]{1,3}\.){3}[0-9]{1,3}|([0-9a-z_!~*'()-]+\.)*([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.[a-z]{2,6})(:[0-9]{1,5})?(\/?|(\/[A-Z0-9a-z_!~*'$().;?:@&,%#\-+=]+)+\/?)$/,
		msg: 'URL Strig no valid.'
	},
	target_url:{
	    regex: /^[^\/].+$/,
	    msg:'URL String no valid.'
	},
    prefixurl : {
        regex : /^((ht|f)tp(s?):\/\/).+$/,
        msg:'Bad Prefix'
    }
};

var Valid = {
	options:{
		focus: 'focus',
		valid: 'v_valid',
		invalid: 'v_invalid',
		norequired: 'v_norequired',
		blink: 'blink',
		maxlength: true,
		message:'<div class="v_label">errors</div>'
	},

	init: function(inputs){
		this.inputs = [];
		inputs.each(this.addInput);
	},
	reset:function(){
		for(var i=0;i<this.inputs.length;i++){
		    Valid.clear(this.inputs[i]);
		}
	},
	addInput: function(i,input){
		Valid.inputs.push(input);
		$(Valid.inputs[i]).blur(Valid.blur);
	},
	clear:function(e){
		$(e).
			removeClass(Valid.options.valid).
			removeClass(Valid.options.invalid).
			addClass(Valid.options.focus).
			parent().find(".v_label").remove();
	},
	focus: function(e){
		if(!this.disabled){
			Valid.clear(e.target);
		}
	},
	blur:function(e){
			Valid.clear(e.target);
	},
	test: function(background){
		if(!this.disabled){
			this.reset();
			this.status = true;
			this.errors = {};  
			for(var i=0;i<this.inputs.length;i++){
			    var input = this.inputs[i];
			    var $input = $(input);
               //	console.log(rule.msg);
				var ruleName = input.className.split(' ')[0];
				var rule = iRules[ruleName];
					if((input.getAttribute('invalid') && input.getAttribute('invalid')=='1') || (input.value!='' || !$(input).hasClass(this.options.norequired)) && !rule.regex.test(input.value)){
						if(!background){
							$(input).addClass(this.options.invalid);
						}
						this.errors[input.name] = {};
                        this.errors[input.name].title = $(input).prevAll('label').text();
                       	this.errors[input.name].msg = ( iRules.messages[ruleName] ? iRules.messages[ruleName] : rule.msg );
						this.status=false;
						//$input.parent().append( this.options.message.replace( /errors/ , rule.msg  ) ); 
					}else if(!background){
						$(input).addClass(this.options.valid);
					}
			}
			return this.status;
		}else{
			return null;
		}
	},
	displayErrors: function(box){
		console.log("errores ----------")
		var error = '';
		for(var i in this.errors){
            error += '- '+ Valid.errors[i].title+': '+this.errors[i].msg +'\n';
		}
		if(error!=''){
		    var title = ( iRules.messages.title ) ?  iRules.messages.title : iRules.core.title;
			var errors = this.errors;
			if(box){
				box.empty();
				$('<h4>'+title+' :</h4>').appendTo(box);
				var list = $('<ul></ul>').appendTo(box);
				for(var i in errors){
					$('<li></li>').html('<b>'+errors[i].title.toUpperCase()+'</b>: '+errors[i].msg).appendTo(list);
				}
			}else{
				alert(title+':\n\n'+error);
			}
		}
	}

};
