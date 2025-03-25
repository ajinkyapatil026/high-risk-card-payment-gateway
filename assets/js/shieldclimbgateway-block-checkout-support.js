( function( blocks, i18n, element, components, editor ) {
    const { registerPaymentMethod } = wc.wcBlocksRegistry;
    // Use the localized data from PHP
    const shieldclimbgateways = shieldclimbgatewayData || [];
    shieldclimbgateways.forEach( ( shieldclimbgateway ) => {
        registerPaymentMethod({
            name: shieldclimbgateway.id,
            label: shieldclimbgateway.label,
            ariaLabel: shieldclimbgateway.label,
            content: element.createElement(
                'div',
                { className: 'shieldclimbgateway-method-wrapper' },
                element.createElement( 
                    'div', 
                    { className: 'shieldclimbgateway-method-label' },
                    '' + shieldclimbgateway.description 
                ),
                shieldclimbgateway.icon_url ? element.createElement(
                    'img', 
                    { 
                        src: shieldclimbgateway.icon_url,
                        alt: shieldclimbgateway.label,
                        className: 'shieldclimbgateway-method-icon'
                    }
                ) : null
            ),
            edit: element.createElement(
                'div',
                { className: 'shieldclimbgateway-method-wrapper' },
                element.createElement( 
                    'div', 
                    { className: 'shieldclimbgateway-method-label' },
                    '' + shieldclimbgateway.description 
                ),
                shieldclimbgateway.icon_url ? element.createElement(
                    'img', 
                    { 
                        src: shieldclimbgateway.icon_url,
                        alt: shieldclimbgateway.label,
                        className: 'shieldclimbgateway-method-icon'
                    }
                ) : null
            ),
            canMakePayment: () => true,
        });
    });
} )(
    window.wp.blocks,
    window.wp.i18n,
    window.wp.element,
    window.wp.components,
    window.wp.blockEditor
);