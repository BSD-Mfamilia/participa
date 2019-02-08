SimpleCaptcha.setup do |sc|
  # default: 100x28
  sc.image_size = '300x40'

  # default: 5
  sc.length = 6

  # default: simply_blue
  # possible values:
  # 'embosed_silver',
  # 'simply_red',
  # 'simply_green',
  # 'simply_blue',
  # 'distorted_black',
  # 'all_black',
  # 'charcoal_grey',
  # 'almost_invisible'
  # 'random'
  #sc.image_style = 'simply_green'

  # default: low
  # possible values: 'low', 'medium', 'high', 'random'
  sc.distortion = 'medium'

  # default: medium
  # possible values: 'none', 'low', 'medium', 'high'
  sc.implode = 'medium'

  sc.image_style = 'simply_green'
  sc.add_image_style('simply_green', [
      "-background '#004732;'",
      "-fill '#FFFFFF'",
      "-border 3",
      "-bordercolor '#54efa5'"])
end