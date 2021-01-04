import { SelectControl } from '@wordpress/components';
import Text from '@activecampaign/camp-components-text';

// 'Import' the native React Component from the WP JS API, as it doesn't appear to be in a declarative import package.
const Component = wp.element.Component;

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 * It was discovered reading online that you can pass a full React Component to the edit
 * property. This gives access to the fuller featured lifecycle event, state events, and render.
 * Otherwise the component is effectively stateless as all logic is implicitly within the render()
 * scope.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @param {Object} [props]           Properties passed from the editor.
 *
 * @return {Component} 				React Component
 */
export default class extends Component{

	constructor(props) {
		super(...arguments);
		this.props = props;

		this.previewCont = React.createRef();

		this.state = {
			previewHtml: {__html:'<p>Please select an ActiveCampaign form.</p>'},
			cssState: false
		}

		let forms = this.props.attributes.settings_activecampaign.forms;
		if( !forms ) {
			this.updatePreview('<p>Please return to settings and select forms to store locally</p>');
			return;
		}

		this.options = [
			{
				label: 'Select Form...',
				value: '0'
			}
		];
		this.forms = Object.values(forms);
		for (let i = 0; i < this.forms.length; i++) {
			let form = this.forms[i];
			this.options.push({
				label: form.name,
				value: form.id
			});
		}

		// Autoload on block init.
		this.bindScript(this.props.attributes.formId, this.props.attributes.useCss);

	}

	filterCssParam(formSrc, useCss){
		let output = formSrc;
		if(useCss === '1'){
			output = formSrc.replace('nostyles=1', 'nostyles=0');
		}
		else if(useCss === '0'){
			output = formSrc.replace('nostyles=0', 'nostyles=1');
		}

		// Bind final css state for previewing
		if(output.indexOf('nostyles=0') > -1){
			this.setState({ cssState: true });
		}
		else{
			this.setState({ cssState: false });
		}

		return output;
	}

	getForm(formId){
		if(!formId || formId === '0'){
			return undefined;
		}
		const currentForm = this.forms.filter((form) => { return form.id === formId });

		if(currentForm && currentForm.length > 0){
			return currentForm[0];
		}
		// Return the default select
		return undefined;
	}

	bindScript(formId, useCss){
		const selected = this.getForm(formId);
		if(selected && selected.version === 2 ){
			this.updatePreview('<p>Please select an ActiveCampaign form.</p>');
			this.loadIframe(formId, this.filterCssParam(selected.script_src, useCss));
		}
		else{
			this.updatePreview('<p>Please select an ActiveCampaign form.</p>');
		}
	}

	updatePreview(content){
		this.setState({ previewHtml : {__html:content} });

		// Check for forms and disable;
		if(this.previewCont.current){
			let formTag = this.previewCont.current.getElementsByTagName('form');
			if(formTag.length){
				formTag[0].onsubmit = function(event){ event.preventDefault(); return false; }
			}
		}
	}

	loadIframe(formId, formSrc){
		if(!window.hasOwnProperty('activcampaignBlockCache')){
			window.activcampaignBlockCache = {};
		}

		if(window.activcampaignBlockCache.hasOwnProperty(formSrc)){
			// Load cached instead of iframe
			let formCont = document.createElement('div');
			formCont.innerHTML = window.activcampaignBlockCache[formSrc];
			this.updatePreview(formCont.innerHTML);
		}
		else{
			// Load iframe and poll for content changes to scrape HTML/CSS
			let iframe = document.createElement('iframe');
			iframe.setAttribute('id', 'iframe');
			iframe.style.width = '0';
			iframe.style.height = '0';
			iframe.style.visibility = 'hidden';
			iframe.frameBorder = '0';
			iframe.style.border = 'none';
			iframe.onload = () => {
				// Inject the embed directly into the iframe instance for scraping
				let doc = iframe.contentDocument ? iframe.contentDocument : iframe.contentWindow.document;
				let body = doc.body;
				let form = document.createElement('div');
				form.setAttribute('class', '_form_'+formId);
				let script = document.createElement('script');
				script.setAttribute('type', 'text/javascript');
				script.setAttribute('src', formSrc);
				body.appendChild(form);
				body.appendChild(script);

				// Honestly it's not very expensive to just lazy watch the document load
				setTimeout(() => { this.watcher(form, formSrc, doc, iframe); }, 100);
			};
			document.body.appendChild(iframe);
		}
	}

	watcher(form, formSrc, doc, iframe){
		if(!doc.getElementsByTagName('form').length){
			setTimeout(() => { this.watcher(form, formSrc, doc, iframe) }, 100);
		}
		else{
			// Trigger the scrap and cache, remove the iframe
			let scripts = form.getElementsByTagName('script');
			let i = scripts.length;
			while (i--) {
				scripts[i].parentNode.removeChild(scripts[i]);
			}

			let formTag = form.getElementsByTagName('form');
			if(formTag.length){
				formTag[0].action = '';
			}

			let formCont = document.createElement('div');
			formCont.innerHTML = form.innerHTML;
			window.activcampaignBlockCache[formSrc] = formCont.innerHTML;
			this.updatePreview(formCont.innerHTML);

			document.body.removeChild(iframe);
		}
	}

	render() {
		let cssFlag = (this.state.cssState) ? 'use-css':'no-css';
		return (
			<div>
				<div className={'wp-activecampaign-form-block'}>
					<div className={'activecampaign-block-header'}>
						<span className={'activecampaign-block-logo'}>
							<svg width="20" height="20" viewBox="0 0 288 288" role="img"><path d="M35.8 0H251.8C271.7 0 287.8 16.1 287.8 36V252C287.8 271.9 271.7 288 251.8 288H35.8C15.9 288 -0.200012 271.9 -0.200012 252V36C-0.200012 16.1 15.9 0 35.8 0Z" fill="#356AE6"></path><path d="M146.6 150.7C149.2 150.7 151.8 149.7 154.6 147.6C157.9 145.3 160.8 143.4 160.8 143.4L161.8 142.7L160.8 142C160.4 141.7 116.9 111.5 112.4 108.6C110.3 107.1 107.9 106.8 106 107.7C104.2 108.6 103.2 110.5 103.2 112.8V123.1L103.6 123.3C103.9 123.5 133.8 144.3 139.6 148.2C142 149.9 144.3 150.7 146.6 150.7Z" fill="#ffffff"></path><path d="M204.9 132.6C202.3 130.7 109 65.6 105 62.8L103.7 61.9V77.9C103.7 83 106.4 84.9 109.7 87.3C109.7 87.3 181.3 137.2 190.2 143.3C181.3 149.5 113.9 196.2 109.6 198.9C104.5 202.3 104 204.5 104 209.1V226.3C104 226.3 202.7 155.7 204.8 154.1C209.3 150.7 210.3 146.7 210.4 143.8V142C210.5 138.4 208.6 135.2 204.9 132.6Z" fill="#ffffff"></path></svg>
						</span>
						<Text mb="60" size="16px" color="black">{'Select an ActiveCampaign Form'}</Text>
					</div>
					<div className={'activecampaign-block-inputs'}>
						<div className={'activecampaign-form-select'}>
							<SelectControl
								className = {'activecampaign-select-control'}
								value={ this.props.attributes.formId }
								options={ this.options }
								onChange={ ( val ) => {
									this.props.setAttributes( { formId: val } );
									this.bindScript(val, this.props.attributes.useCss);
								}}
							/>
						</div>
						<div className={'activecampaign-css-select'}>
							<SelectControl
								className = {'activecampaign-select-control'}
								value={ this.props.attributes.useCss }
								options={ [
									{ label: 'Use CSS From Settings', value: 'global'},
									{ label: 'Use ActiveCampaign CSS', value: '1'},
									{ label: 'Don\'t Use CSS', value: '0'},
								] }
								onChange={ ( val ) => {
									this.props.setAttributes( { useCss: val } );
									this.bindScript(this.props.attributes.formId, val);
								}}
							/>
						</div>
					</div>
				</div>
				<div className={`activecampaign-form-preview ${cssFlag} ${this.props.className}`} id={`activecampaignFormPreview${this.props.clientId}`} ref={this.previewCont} dangerouslySetInnerHTML={this.state.previewHtml}></div>
			</div>
		);
	}
}




