import { useI18n } from "vue-i18n";

const fields = () => {
  const { t } = useI18n();

  // API endpoints
  const url = "payment-providers?fields=id,xid,name,is_active,payment_mode_name";
  const addEditUrl = "payment-providers";

  // Default form data
  const initData = {
    name: "",
    code: "",
    payment_mode_id: null,
    env: "sandbox",
    is_active: true,
    priority: 1,
    config: {},
  };

  // Table columns
  const columns = [
    {
      title: t("payment_provider.name"),
      dataIndex: "name",
    },
    {
      title: t("payment_provider.code"),
      dataIndex: "code",
    },
    {
      title: t("payment_provider.payment_mode"),
      dataIndex: "payment_mode_id",
    },
    {
      title: t("payment_provider.environment"),
      dataIndex: "env",
    },
    {
      title: t("common.status"),
      dataIndex: "is_active",
    },
    {
      title: t("common.action"),
      dataIndex: "action",
      width: 120,
    },
  ];

  // Searchable columns
  const filterableColumns = [
    {
      key: "name",
      value: t("payment_provider.name"),
    },
    {
      key: "code",
      value: t("payment_provider.code"),
    },
  ];

  return {
    url,
    addEditUrl,
    initData,
    columns,
    filterableColumns,
  };
};

export default fields;
