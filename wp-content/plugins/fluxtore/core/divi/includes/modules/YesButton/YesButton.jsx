// External Dependencies
import React, { Component } from 'react';

// Internal Dependencies
import './style.css';


class YesButton extends Component {

  static slug = 'fldt_yes_button';

  render() {    
    
    const {
      button_url= '#',
      button_text = 'Click Here',
      button_alignment,
      button_text_size,
      button_text_color,
      button_bg_color,
      button_border_width,
      button_border_color,
      button_letter_spacing,          
      animation_style,     
      button_on_hover,
      background_layout,          
      module_class,
      moduleInfo,
      dynamic,
      url_new_window,
      button_font, 
      custom_padding,
      custom_margin,
      button_border_radius          
    } = this.props;
   
    
    const [fontFamily, fontWeight, fontStyle] = button_font ? button_font.split('|') : [];
    const [paddingTop, paddingRight, paddingBottom, paddingLeft] = custom_padding ? custom_padding.split('|') : [];
    const [marginTop, marginRight, marginBottom, marginLeft] = custom_margin ? custom_margin.split('|') : [];
    const borderRadius = button_border_radius ? button_border_radius : 0;
    
    const buttonStyle = {
      backgroundColor: button_bg_color,
      color: button_text_color,
      borderWidth: button_border_width,
      borderColor: button_border_color,
      borderRadius: borderRadius,
      fontSize: button_text_size,
      letterSpacing: button_letter_spacing, 
      fontFamily: fontFamily,
      fontWeight: fontWeight,
      fontStyle: fontStyle, 
      paddingTop: paddingTop,
      paddingRight: paddingRight,
      paddingBottom: paddingBottom,
      paddingLeft: paddingLeft, 
      marginTop: marginTop,
      marginRight: marginRight, 
      marginBottom: marginBottom,
      marginLeft: marginLeft,                  
    };

    const divStyle = {
      textAlign: button_alignment,      
    };
   
    const wrapperClassName = `et_pb_button_module_wrapper et-fb-module-wrapper ${moduleInfo.orderClassName}_wrapper et_pb_button_alignment_${button_alignment} et_pb_module ${button_on_hover === "on" && 'et-fb-module--has-mousetrap'}`;
    const buttonClassName = `et_pb_button ${moduleInfo.type} ${moduleInfo.orderClassName} et_pb_button_${button_alignment} et_pb_bg_layout_${background_layout} et_pb_module ${button_on_hover === "on" && 'et-fb-module--has-mousetrap'} ${animation_style && 'et-animated--vb'} ${module_class ? module_class : ''}`;
    
    return (
      <div className={wrapperClassName} data-address={moduleInfo.address} style={divStyle} >
        <a
          style={buttonStyle}
          href={button_url}
          className={buttonClassName}
          target={url_new_window === "on" ? '_blank' : '_self'}
          dynamic={dynamic}          
        >          
          {button_text}         
        </a>
        
        </div>
    );
  }
}

export default YesButton;
