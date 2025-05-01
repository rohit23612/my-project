// External Dependencies
import React, { Component } from 'react';

// Internal Dependencies
import './style.css';

class Checkout extends Component {
  static slug = 'fldt_checkout';

  render() {
    const {
      shortcode_content,
      // alignment,
      // text_color,
      // bg_color,
      // border_width,
      // border_color,
      // text_size,
      // letter_spacing,
      // font,
      // custom_padding,
      // custom_margin,
      // border_radius,
    } = this.props;

    // const [fontFamily, fontWeight, fontStyle] = font ? font.split('|') : [];
    // const [paddingTop, paddingRight, paddingBottom, paddingLeft] = custom_padding
    //   ? custom_padding.split('|')
    //   : [];
    // const [marginTop, marginRight, marginBottom, marginLeft] = custom_margin
    //   ? custom_margin.split('|')
    //   : [];
    // const borderRadius = border_radius ? border_radius : 0;

    // const wrapperStyle = {
    //   backgroundColor: bg_color,
    //   color: text_color,
    //   borderWidth: border_width,
    //   borderColor: border_color,
    //   borderRadius: borderRadius,
    //   fontSize: text_size,
    //   letterSpacing: letter_spacing,
    //   fontFamily: fontFamily,
    //   fontWeight: fontWeight,
    //   fontStyle: fontStyle,
    //   paddingTop: paddingTop,
    //   paddingRight: paddingRight,
    //   paddingBottom: paddingBottom,
    //   paddingLeft: paddingLeft,
    //   marginTop: marginTop,
    //   marginRight: marginRight,
    //   marginBottom: marginBottom,
    //   marginLeft: marginLeft,
    //   textAlign: alignment,
    // };

    return (
      <div className={`fluxtore_checkout ${this.props.moduleClass}`}>
        <div dangerouslySetInnerHTML={{ __html: shortcode_content }}></div>
        <div>[woocommerce_checkout]</div>
      </div>
    );
  }
}

export default Checkout;

