module ActiveAdmin
  module Views
    class Footer < Component

      def build
        super :id => "footer"                                                    
        super :style => "text-align: right;"                                     

        div do                                                                   
          small do
            #link_to "Manual de uso de datos de carácter personal", "/pdf/manual-usuario-administracion.pdf", target: "_blank"
          end
        end
      end

    end
  end
end
