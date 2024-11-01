<?php
namespace Solaceform\Widgets;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Solaceform\SolaceFormBase;

/**
 * Solace Form Builder Widget.
 *
 * @since 1.0.0
 */
class SolaceFormBuilder extends SolaceFormBase {
	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'solaceform';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Solace Form Builder', 'solace-extra' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	/**
	 * Retrieve the list of scripts the widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array( 'solaceform' );
	}

	/**
	 * Retrieve the list of styles the widget depended on.
	 *
	 * Used to set styles dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget styles dependencies.
	 */
	public function get_style_depends() {
		return array( 'solaceform' );
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_fields',
			array(
				'label' => __( 'Form Fields', 'solace-extra' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'field_type',
			array(
				'label'   => __( 'Type', 'solace-extra' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'Text',
				'options' => array(
					'Text'      => __( 'Text', 'solace-extra' ),
					'Email'     => __( 'Email', 'solace-extra' ),
					'Textarea'  => __( 'Textarea', 'solace-extra' ),
					'URL'       => __( 'URL', 'solace-extra' ),
					'Tel'       => __( 'Tel', 'solace-extra' ),
					'Radio'     => __( 'Radio', 'solace-extra' ),
					'Select'    => __( 'Select', 'solace-extra' ),
					'Checkbox'  => __( 'Checkbox', 'solace-extra' ),
					'Number'    => __( 'Number', 'solace-extra' ),
					'Date'      => __( 'Date', 'solace-extra' ),
					'Time'      => __( 'Time', 'solace-extra' ),
					'File'      => __( 'File', 'solace-extra' ),
					'Password'  => __( 'Password', 'solace-extra' ),
					'HTML'      => __( 'HTML', 'solace-extra' ),
					'Hidden'    => __( 'Hidden', 'solace-extra' ),
					'reCAPTCHA' => __( 'reCAPTCHA', 'solace-extra' ),
				),
			)
		);

		$repeater->add_control(
			'rows',
			array(
				'label'      => __( 'Rows', 'solace-extra' ),
				'type'       => Controls_Manager::NUMBER,
				'default'    => 4,
				'conditions' => array(
					'terms' => array(
						array(
							'name'  => 'field_type',
							'value' => 'Textarea',
						),
					),
				),
			)
		);

		$repeater->add_control(
			'field_options',
			array(
				'label'       => __( 'Options', 'solace-extra' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => __( 'Enter each option in a new line.', 'solace-extra' ),
				'conditions'  => array(
					'terms' => array(
						array(
							'name'     => 'field_type',
							'operator' => 'in',
							'value'    => array(
								'Select',
								'Checkbox',
								'Radio',
							),
						),
					),
				),
			)
		);

		$repeater->add_control(
			'file_types',
			array(
				'label'       => __( 'Allowed File Types', 'solace-extra' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( '.pdf,.jpg,.txt', 'solace-extra' ),
				'conditions'  => array(
					'terms' => array(
						array(
							'name'  => 'field_type',
							'value' => 'File',
						),
					),
				),
			)
		);

		$repeater->add_control(
			'multiple_files',
			array(
				'label'        => __( 'Multiple Files', 'solace-extra' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'multiple_files',
				'conditions'   => array(
					'terms' => array(
						array(
							'name'  => 'field_type',
							'value' => 'File',
						),
					),
				),
			)
		);

		$repeater->add_control(
			'field_html',
			array(
				'label'      => __( 'HTML', 'solace-extra' ),
				'type'       => Controls_Manager::TEXTAREA,
				'conditions' => array(
					'terms' => array(
						array(
							'name'  => 'field_type',
							'value' => 'HTML',
						),
					),
				),
			)
		);

		$repeater->add_control(
			'type_hr',
			array(
				'type'       => Controls_Manager::DIVIDER,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'field_type',
							'operator' => '!in',
							'value'    => array(
								'reCAPTCHA',
								'Hidden',
								'HTML',
							),
						),
					),
				),
			)
		);

		$repeater->add_control(
			'field_label',
			array(
				'label'      => __( 'Label', 'solace-extra' ),
				'type'       => Controls_Manager::TEXT,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'field_type',
							'operator' => '!in',
							'value'    => array(
								'Hidden',
								'reCAPTCHA',
							),
						),
					),
				),
			)
		);

		$repeater->add_control(
			'site_key',
			array(
				'label'      => __( 'Site Key', 'solace-extra' ),
				'type'       => Controls_Manager::TEXT,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'field_type',
							'operator' => 'in',
							'value'    => array(
								'reCAPTCHA',
							),
						),
					),
				),
			)
		);

		$repeater->add_control(
			'field_placeholder',
			array(
				'label'      => __( 'Placeholder', 'solace-extra' ),
				'type'       => Controls_Manager::TEXT,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'field_type',
							'operator' => '!in',
							'value'    => array(
								'reCAPTCHA',
								'Hidden',
								'HTML',
								'Radio',
								'Checkbox',
								'Select',
								'Date',
								'Time',
								'File',
							),
						),
					),
				),
			)
		);

		$repeater->add_control(
			'field_default_value',
			array(
				'label'      => __( 'Default value', 'solace-extra' ),
				'type'       => Controls_Manager::TEXT,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'field_type',
							'operator' => 'in',
							'value'    => array(
								'Text',
								'Email',
								'URL',
								'Tel',
								'Number',
								'Hidden',
							),
						),
					),
				),
			)
		);

		$repeater->add_control(
			'field_name',
			array(
				'label'       => __( 'Name', 'solace-extra' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Name is required. It is used to send the data to your email.', 'solace-extra' ),
				'conditions'  => array(
					'terms' => array(
						array(
							'name'     => 'field_type',
							'operator' => '!in',
							'value'    => array(
								'reCAPTCHA',
								'HTML',
							),
						),
					),
				),
			)
		);

		$repeater->add_control(
			'width_hr',
			array(
				'type'       => Controls_Manager::DIVIDER,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'field_type',
							'operator' => '!in',
							'value'    => array(
								'reCAPTCHA',
								'Hidden',
							),
						),
					),
				),
			)
		);

		$repeater->add_responsive_control(
			'field_width',
			array(
				'label'      => __( 'Column Width', 'solace-extra' ),
				'type'       => Controls_Manager::SELECT,
				'default'    => '100',
				'options'    => array(
					'20'  => __( '20%', 'solace-extra' ),
					'25'  => __( '25%', 'solace-extra' ),
					'33'  => __( '33%', 'solace-extra' ),
					'40'  => __( '40%', 'solace-extra' ),
					'50'  => __( '50%', 'solace-extra' ),
					'60'  => __( '60%', 'solace-extra' ),
					'66'  => __( '66%', 'solace-extra' ),
					'75'  => __( '75%', 'solace-extra' ),
					'80'  => __( '80%', 'solace-extra' ),
					'100' => __( '100%', 'solace-extra' ),
				),
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'field_type',
							'operator' => '!in',
							'value'    => array(
								'Hidden',
								'reCAPTCHA',
							),
						),
					),
				),
			)
		);

		$repeater->add_control(
			'required_hr',
			array(
				'type'       => Controls_Manager::DIVIDER,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'field_type',
							'operator' => '!in',
							'value'    => array(
								'Checkbox',
								'reCAPTCHA',
								'Hidden',
								'HTML',
								'Radio',
								'Checkbox',
								'Select',
							),
						),
					),
				),
			)
		);

		$repeater->add_control(
			'field_required',
			array(
				'label'        => __( 'Required', 'solace-extra' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'solace-extra' ),
				'label_off'    => __( 'No', 'solace-extra' ),
				'return_value' => 'yes',
				'conditions'   => array(
					'terms' => array(
						array(
							'name'     => 'field_type',
							'operator' => '!in',
							'value'    => array(
								'Checkbox',
								'reCAPTCHA',
								'Hidden',
								'HTML',
								'Radio',
								'Checkbox',
								'Select',
							),
						),
					),
				),
			)
		);

		$repeater->add_control(
			'class_hr',
			array(
				'type'       => Controls_Manager::DIVIDER,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'field_type',
							'operator' => '!in',
							'value'    => array(
								'reCAPTCHA',
								'Hidden',
								'HTML',
								'Radio',
								'Checkbox',
								'Select',
							),
						),
					),
				),
			)
		);

		$repeater->add_control(
			'field_class',
			array(
				'label'      => __( 'Custom Class', 'solace-extra' ),
				'type'       => Controls_Manager::TEXT,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'field_type',
							'operator' => '!in',
							'value'    => array(
								'reCAPTCHA',
								'Hidden',
								'HTML',
								'Radio',
								'Checkbox',
								'Select',
							),
						),
					),
				),
			)
		);

		$repeater->add_control(
			'field_id',
			array(
				'label'      => __( 'Custom ID', 'solace-extra' ),
				'type'       => Controls_Manager::TEXT,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'field_type',
							'operator' => '!in',
							'value'    => array(
								'HTML',
								'Radio',
								'Checkbox',
								'Select',
								'reCAPTCHA',
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'fields',
			array(
				'label'       => __( 'Fields', 'solace-extra' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'field_type'  => 'Text',
						'field_name'  => 'name',
						'field_label' => 'Name',
					),
					array(
						'field_type'  => 'Email',
						'field_name'  => 'email',
						'field_label' => 'Email',
					),
					array(
						'field_type'  => 'Textarea',
						'field_name'  => 'msg',
						'field_label' => 'Message',
					),
				),
				'title_field' => '{{{ field_type }}}',
			)
		);

		$this->add_control(
			'list_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'show_label',
			array(
				'label'        => __( 'Label', 'solace-extra' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Show', 'solace-extra' ),
				'label_off'    => __( 'Hide', 'solace-extra' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_required_mark',
			array(
				'label'        => __( 'Required Mark', 'solace-extra' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'solace-extra' ),
				'label_off'    => __( 'Hide', 'solace-extra' ),
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_button',
			array(
				'label' => __( 'Button', 'solace-extra' ),
			)
		);

		$this->add_control(
			'button_text_align',
			array(
				'label'     => __( 'Alignment', 'solace-extra' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'solace-extra' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'solace-extra' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'solace-extra' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'default'   => 'center',
				'toggle'    => true,
				'selectors' => array(
					'{{WRAPPER}} .solaceform-form-button-wrap' => 'text-align: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'   => __( 'Text', 'solace-extra' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Send',
			)
		);

		$this->add_control(
			'button_icon',
			array(
				'label'   => __( 'Icon', 'solace-extra' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-star',
					'library' => 'solid',
				),
			)
		);

		$this->add_control(
			'button_icon_position',
			array(
				'label'   => __( 'Icon Position', 'solace-extra' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left'  => array(
						'title' => __( 'Left', 'solace-extra' ),
						'icon'  => 'fa fa-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'solace-extra' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'default' => 'left',
				'toggle'  => true,
			)
		);

		$this->add_control(
			'button_id',
			array(
				'label' => __( 'Button ID', 'solace-extra' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_messages',
			array(
				'label' => __( 'Messages', 'solace-extra' ),
			)
		);

		$this->add_control(
			'success_message',
			array(
				'label'   => __( 'Success Message', 'solace-extra' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'Your message has been sent', 'solace-extra' ),
				'rows'    => 5,
			)
		);

		$this->add_control(
			'error_message',
			array(
				'label'   => __( 'Error Message', 'solace-extra' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'Can\'t send the email', 'solace-extra' ),
				'rows'    => 5,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_redirect',
			array(
				'label' => __( 'Redirect', 'solace-extra' ),
			)
		);

		$this->add_control(
			'redirect',
			array(
				'label'        => __( 'Redirect to another URL', 'solace-extra' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'solace-extra' ),
				'label_off'    => __( 'No', 'solace-extra' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'redirect_url',
			array(
				'label'       => __( 'Redirect To', 'solace-extra' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'https://your-link.com', 'solace-extra' ),
				'condition'   => array(
					'redirect' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_email',
			array(
				'label' => __( 'Email', 'solace-extra' ),
			)
		);

		$this->add_control(
			'email_to',
			array(
				'label' => __( 'To', 'solace-extra' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'email_subject',
			array(
				'label' => __( 'Subject', 'solace-extra' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'subject_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'email_from',
			array(
				'label' => __( 'From Email', 'solace-extra' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'email_name',
			array(
				'label' => __( 'From Name', 'solace-extra' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_field_style',
			array(
				'label' => __( 'Fields', 'solace-extra' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		
		$field_selector = '{{WRAPPER}} .solaceform-style-field, {{WRAPPER}} .solace-rico';
		
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'field_typography',
				'selector' => $field_selector,
			]
		);
		
		$this->add_control(
			'field_color',
			[
				'label' => __( 'Color', 'solace-extra' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#000000', // Atau pilih warna default lain sesuai kebutuhan
				'selectors' => [
					$field_selector => 'color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_responsive_control(
			'field_padding',
			[
				'label' => __( 'Padding', 'solace-extra' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					$field_selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'field_margin',
			[
				'label' => __( 'Margin', 'solace-extra' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					$field_selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'field_border',
				'selector' => $field_selector,
			]
		);
		
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'field_background',
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => $field_selector,
			]
		);
		
		$this->end_controls_section();
		

		$this->start_controls_section(
			'section_button_style',
			array(
				'label' => __( 'Button', 'solace-extra' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$btn_selector = '{{WRAPPER}} .solaceform-form-button';

		$this->typography( 'btn_typography', $btn_selector );
		$this->color( 'btn_color', $btn_selector );
		$this->padding( 'btn_padding', $btn_selector );
		$this->margin( 'btn_margin', $btn_selector );
		$this->border( 'btn_border', $btn_selector );
		$this->background( 'btn_background', $btn_selector );

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$fields   = $settings['fields'];
		?>

		<form class="solaceform-form" method="post" data-post_id="<?php echo esc_attr( get_the_ID() ); ?>" data-el_id="<?php echo esc_attr( $this->get_id() ); ?>">
		<?php
		if ( $fields ) {
			foreach ( $fields as $field ) {
				$width  = $field['field_width'] ? $field['field_width'] : '';
				$params = array(
					'type'           => $field['field_type'] ? strtolower( $field['field_type'] ) : '',
					'label'          => $field['field_label'] ? $field['field_label'] : '',
					'placeholder'    => $field['field_placeholder'] ? $field['field_placeholder'] : '',
					'value'          => $field['field_default_value'] ? $field['field_default_value'] : '',
					'name'           => $field['field_name'] ? $field['field_name'] : '',
					'width'          => $field['field_width'] ? $field['field_width'] : '',
					'required'       => $field['field_required'] ? $field['field_required'] : '',
					'id'             => $field['field_id'] ? $field['field_id'] : '',
					'class'          => $field['field_class'] ? $field['field_class'] : '',
					'rows'           => $field['rows'] ? $field['rows'] : '',
					'options'        => $field['field_options'] ? $field['field_options'] : '',
					'multiple_files' => $field['multiple_files'] ? $field['multiple_files'] : '',
					'file_types'     => $field['file_types'] ? $field['file_types'] : '',
					'html'           => $field['field_html'] ? $field['field_html'] : '',
					'is_label'       => $settings['show_label'] ? true : false,
					'is_mark'        => $settings['show_required_mark'] ? true : false,
				);

				echo '<div class="solaceform-fields elementor-repeater-item-' . esc_attr( $field['_id'] ) . ' efb-field-width-' . esc_attr( $width ) . '">';

				switch ( $field['field_type'] ) {
					case 'Text':
					case 'URL':
					case 'Tel':
					case 'Number':
					case 'Date':
					case 'Time':
					case 'File':
					case 'Password':
					case 'Email': {
						$this->input( $params );
						break;
					}

					case 'Textarea': {
						$this->textarea( $params );
						break;
					}

					case 'Select':
					case 'Checkbox':
					case 'Radio': {
						$this->multi( $params );
						break;
					}

					case 'HTML': {
						$this->html( $params['html'], $params['label'], $params['is_label'] );
						break;
					}

					case 'Hidden': {
						$this->hidden( $params['value'], $params['name'], $params['id'], );
						break;
					}

					case 'reCAPTCHA': {
						$this->reCAPTCHA( $field['site_key'] );

						break;
					}

					default:
						break;
				}

				echo '</div>';
			}
		}

		$this->button(
			$settings['button_text'],
			$settings['button_icon'],
			$settings['button_icon_position'],
			$settings['button_id']
		);
		?>

		</form>
		<div class="solaceform-form-msg"></div>
		<?php
	}
}
