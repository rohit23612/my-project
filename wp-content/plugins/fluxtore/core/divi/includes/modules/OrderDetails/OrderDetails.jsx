// External Dependencies
import React, { Component, Fragment } from 'react';

// Internal Dependencies
import './style.css';


class OrderDetails extends Component {

  static slug = 'fldt_order_details';
 
  render() {        
    const {      
      border_radii,
      border_width_all,
      border_color_all,
      border_style_all,
      border_style_top,
      border_style_bottom,
      border_style_left,
      border_style_right,
      border_width_top,  
      border_width_right,
      border_width_bottom,
      border_width_left, 
      border_color_top,
      border_color_right,
      border_color_bottom,
      border_color_left,  
    } = this.props;

    let borderWidth; 
    let borderWidthAll = border_width_all ? border_width_all : 0;
    borderWidth = `${border_width_top ? border_width_top : borderWidthAll} ${border_width_right ? border_width_right : borderWidthAll} ${border_width_bottom ? border_width_bottom : borderWidthAll} ${border_width_left ? border_width_left : borderWidthAll}`;
     

    let borderRadii = border_radii && border_radii.split('|');
    let borderRadius;
    if(borderRadii[0] === "on"){
      borderRadius = `${borderRadii[1]} ${borderRadii[2]} ${borderRadii[3]} ${borderRadii[4]}`;
    }    

    let mainStyle = {borderWidth, borderTopColor: `${border_color_top ? border_color_top : border_color_all}`, borderRightColor: `${border_color_right ? border_color_right : border_color_all}`, borderBottomColor: `${border_color_bottom ? border_color_bottom : border_color_all}`, borderLeftColor: `${border_color_left ? border_color_left : border_color_all}`, borderRadius, borderStyle: border_style_all ? border_style_all : 'solid', borderTopStyle: border_style_top ? border_style_top : "solid", borderRightStyle: border_style_right ? border_style_right : "solid", borderBottomStyle: border_style_bottom ? border_style_bottom : "solid", borderLeftStyle: border_style_left ? border_style_left : "solid"};
        
     // const wrapperClassName = `et_pb_module ${moduleInfo.orderClassName} ${moduleInfo.orderClassName}_wrapper`;
   
    return (      
      <Fragment> 
        <div style={mainStyle}>       
        <section className="woocommerce-order-details">
          <h2 className="woocommerce-order-details__title">Order details</h2>
          <table className="woocommerce-table woocommerce-table--order-details shop_table order_details">
            <thead>
              <tr>
                <th className="woocommerce-table__product-name product-name">Product</th>
                <th className="woocommerce-table__product-table product-total">Total</th>
              </tr>
            </thead>
            <tbody>
              <tr className="woocommerce-table__line-item order_item">
                <td className="woocommerce-table__product-name product-name">
                  <a href="https://example.com/product-1">Product 1</a> 
                  <strong className="product-quantity">×&nbsp;1</strong>	
                </td>
                <td className="woocommerce-table__product-total product-total">
                  <span className="woocommerce-Price-amount amount"><bdi>42,00&nbsp;<span className="woocommerce-Price-currencySymbol">€</span></bdi></span>	
                </td>
              </tr>
              <tr className="woocommerce-table__line-item order_item">
                <td className="woocommerce-table__product-name product-name">
                  <a href="https://example.com/product-2">Product 2</a> <strong className="product-quantity">×&nbsp;1</strong>	
                </td>
                <td className="woocommerce-table__product-total product-total">
                  <span className="woocommerce-Price-amount amount"><bdi>16,20&nbsp;<span className="woocommerce-Price-currencySymbol">€</span></bdi></span>	
                </td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <th scope="row">Subtotal:</th>
                <td>
                  <span className="woocommerce-Price-amount amount">58,20&nbsp;<span className="woocommerce-Price-currencySymbol">€</span></span>
                </td>
              </tr>
              <tr>
                <th scope="row">Payment method:</th>
                <td>Method of payment</td>
              </tr>
                <tr>
                  <th scope="row">Total:</th>
                  <td><span className="woocommerce-Price-amount amount">58,20&nbsp;<span className="woocommerce-Price-currencySymbol">€</span></span></td>
              </tr>
            </tfoot>
          </table>
        </section>    
        <section className="woocommerce-customer-details">
          <h2 className="woocommerce-column__title">Billing address</h2>
          <address>
            Address Line 1<br />Address Line 2<br />Address Line 3<br />City Name PinCode<br />State Name, Country Name
            <p className="woocommerce-customer-details--phone">Phone Number</p>
            <p className="woocommerce-customer-details--email">Email Id</p>
          </address>
        </section>   
        </div>     
      </Fragment>
    )
  }

 

}

export default OrderDetails;
