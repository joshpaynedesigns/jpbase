/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#save
 *
 * @return {WPElement} Element to render.
 */
export default function Save({ attributes, className }) {
	if(attributes.formId && parseInt(attributes.formId) > 0) {
		let css = (attributes.useCss === '0' || attributes.useCss === '1')? " css="+attributes.useCss : '';
		return <div className={ className }>{ '[activecampaign form='+attributes.formId+css+']'}</div>;
	} else {
		return <div className={ className }>{'[activecampaign]'}</div>;
	}

}
