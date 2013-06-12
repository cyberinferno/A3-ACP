USE [ASD]
GO

/****** Object:  Table [dbo].[coupon_table]    Script Date: 06/07/2013 19:55:24 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[coupon_table](
	[id] [int] NOT NULL,
	[coupon_code] [varchar](255) NOT NULL,
	[discount] [int] NOT NULL,
	[character] [varchar](255) NOT NULL,
	[min_amt] [int] NOT NULL,
	[gift_time] [varchar](255) NOT NULL,
	[flag] [int] NOT NULL
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO

ALTER TABLE [dbo].[coupon_table] ADD  CONSTRAINT [DF_coupon_table_flag]  DEFAULT ((0)) FOR [flag]
GO

