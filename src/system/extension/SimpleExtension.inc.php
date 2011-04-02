<?php


class SimpleExtenson extends GvKernelExtension
{
    /**
     * Template method for loading extension data.
     *
     * @return ExtensionData
     */
    protected function loadData()
    {
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Query to extension.
     * Redirect request to public function of current extension with same name.
     *
     * @param string $sAction Name of action handler.
     * @param array  $aParams Arguments to extension.
     *
     * @return mixed
     */
    public function query($sAction, $aParams = array())
    {
        GvKernel::instance()->trace->addLine('[%s] Executing query: "%s".', __CLASS__, $sAction);

        // Check existence of method.
        if (!method_exists($this, $sAction)) {
            GvKernel::instance()->trace->addLine('[%s] Handler for query ("%s") is not found.', __CLASS__, $sAction);
            return null;
        }

        // Method must be public.
        $cRefl = new ReflectionMethod($this, $sAction);
        if (!$cRefl->isPublic()) {
            GvKernel::instance()->trace->addLine('[%s] Handler for query ("%s") is not public.', __CLASS__, $sAction);
            return null;
        }
        
        GvKernel::instance()->trace->addLine('[%s] Handler found for query: "%s".', __CLASS__, $sAction);
        
        return call_user_func_array(array($this, $sAction), $aParams);

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------